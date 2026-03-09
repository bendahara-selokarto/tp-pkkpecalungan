param(
    [string]$RepoRoot = '',
    [switch]$Quiet
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Get-RepoRelativePath {
    param(
        [Parameter(Mandatory = $true)]
        [string]$RepoRoot,

        [Parameter(Mandatory = $true)]
        [string]$AbsolutePath
    )

    $uriRoot = New-Object System.Uri(($RepoRoot.TrimEnd('\') + '\'))
    $uriPath = New-Object System.Uri($AbsolutePath)
    return [System.Uri]::UnescapeDataString($uriRoot.MakeRelativeUri($uriPath).ToString()).Replace('/', '\')
}

function Normalize-TargetToken {
    param(
        [Parameter(Mandatory = $true)]
        [AllowEmptyString()]
        [string]$Token
    )

    $normalized = $Token.Trim()
    $normalized = $normalized.Trim('"', '''', '`')
    $normalized = $normalized.TrimEnd('.', ',', ';', ':')

    if ($normalized.Contains('#')) {
        $normalized = $normalized.Split('#')[0]
    }

    if ($normalized.Contains('?')) {
        $normalized = $normalized.Split('?')[0]
    }

    return $normalized.Replace('/', '\')
}

function Test-IgnoredToken {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Token
    )

    if ([string]::IsNullOrWhiteSpace($Token)) {
        return $true
    }

    if ($Token.StartsWith('#')) {
        return $true
    }

    if ($Token -match '^(https?|mailto|tel):') {
        return $true
    }

    if ($Token -match '^(php artisan|npm run|pnpm |yarn |composer |powershell |pwsh |artisan )') {
        return $true
    }

    if ($Token.Contains(' ') -and $Token -notmatch '^docs\\referensi\\') {
        return $true
    }

    if ($Token -match '^[A-Za-z]:\\') {
        return $true
    }

    if ($Token.IndexOfAny(@('*', '<', '>', '{', '}', '$', '|')) -ge 0) {
        return $true
    }

    if ($Token -match '^\$\{\{') {
        return $true
    }

    if ($Token -match '^docs\\referensi\\' -and $Token -notmatch '\.(md|gitkeep)$') {
        return $true
    }

    return $false
}

function Test-RepoLocalToken {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Token
    )

    if ($Token -match '^(AGENTS\.md|README\.md|PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X\.md)$') {
        return $true
    }

    if ($Token -match '^(?:docs|scripts|\.github|app|config|database|routes|resources|tests|public|bootstrap|storage|vendor)\\') {
        return $true
    }

    if ($Token -match '^[.]{1,2}\\') {
        return $true
    }

    if ($Token -match '^(?:AI_[A-Z0-9_]+|MARKDOWN_CONTEXT_SPACE_BUDGET|OPERATIONAL_VALIDATION_LOG|PLANNING_ARTIFACT_INDEX|PROCESS_TODO_ARCHIVE_STRATEGY|COMMAND_NUMBER_SHORTCUTS)\.md$') {
        return $true
    }

    if ($Token -match '^(?:ADR_[0-9]{4}_[A-Z0-9_]+|TODO_[A-Z0-9]{4,8}_[A-Z0-9_]+_\d{4}_\d{2}_\d{2})\.md$') {
        return $true
    }

    return $false
}

function Resolve-RepoTargetPath {
    param(
        [Parameter(Mandatory = $true)]
        [string]$RepoRoot,

        [Parameter(Mandatory = $true)]
        [string]$SourceFile,

        [Parameter(Mandatory = $true)]
        [string]$Token
    )

    if ($Token -match '^(AGENTS\.md|README\.md|PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X\.md)$') {
        return [System.IO.Path]::GetFullPath((Join-Path $RepoRoot $Token))
    }

    if ($Token -match '^[.]{1,2}\\') {
        return [System.IO.Path]::GetFullPath((Join-Path (Split-Path -Parent $SourceFile) $Token))
    }

    if ($Token -notmatch '[\\/]') {
        if ($Token -match '^(?:AI_[A-Z0-9_]+|MARKDOWN_CONTEXT_SPACE_BUDGET|OPERATIONAL_VALIDATION_LOG|PLANNING_ARTIFACT_INDEX|PROCESS_TODO_ARCHIVE_STRATEGY|COMMAND_NUMBER_SHORTCUTS|CODE_PLACEMENT_POLICY)\.md$') {
            return [System.IO.Path]::GetFullPath((Join-Path (Join-Path $RepoRoot 'docs/process') $Token))
        }

        if ($Token -match '^ADR_[0-9]{4}_[A-Z0-9_]+\.md$') {
            return [System.IO.Path]::GetFullPath((Join-Path (Join-Path $RepoRoot 'docs/adr') $Token))
        }

        return [System.IO.Path]::GetFullPath((Join-Path (Split-Path -Parent $SourceFile) $Token))
    }

    if ($Token.StartsWith('\')) {
        return [System.IO.Path]::GetFullPath((Join-Path $RepoRoot $Token.TrimStart('\')))
    }

    return [System.IO.Path]::GetFullPath((Join-Path $RepoRoot $Token))
}

function Add-MissingReference {
    param(
        [System.Collections.Generic.List[string]]$Errors,

        [string]$RepoRoot,

        [string]$SourceFile,

        [int]$LineNumber,

        [string]$Token
    )

    if ([string]::IsNullOrWhiteSpace($Token)) {
        return
    }

    $normalizedToken = Normalize-TargetToken -Token $Token
    if ([string]::IsNullOrWhiteSpace($normalizedToken)) {
        return
    }

    if (Test-IgnoredToken -Token $normalizedToken) {
        return
    }

    if (-not (Test-RepoLocalToken -Token $normalizedToken)) {
        return
    }

    try {
        $targetPath = Resolve-RepoTargetPath -RepoRoot $RepoRoot -SourceFile $SourceFile -Token $normalizedToken
    } catch {
        $sourceRelative = Get-RepoRelativePath -RepoRoot $RepoRoot -AbsolutePath $SourceFile
        $errors.Add("${sourceRelative}:$LineNumber -> invalid path token: $normalizedToken")
        return
    }

    if (Test-Path $targetPath) {
        return
    }

    $sourceRelative = Get-RepoRelativePath -RepoRoot $RepoRoot -AbsolutePath $SourceFile
    $errors.Add("${sourceRelative}:$LineNumber -> missing path: $normalizedToken")
}

if ([string]::IsNullOrWhiteSpace($RepoRoot)) {
    $RepoRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
} else {
    $RepoRoot = (Resolve-Path $RepoRoot).Path
}

$markdownFiles = @()
foreach ($rootMarkdown in @('AGENTS.md', 'README.md', 'PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X.md')) {
    $rootPath = Join-Path $RepoRoot $rootMarkdown
    if (Test-Path $rootPath) {
        $markdownFiles += Get-Item $rootPath
    }
}

$processCanonicalFiles = @(
    'AI_SINGLE_PATH_ARCHITECTURE.md',
    'AI_FRIENDLY_EXECUTION_PLAYBOOK.md',
    'AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md',
    'MARKDOWN_CONTEXT_SPACE_BUDGET.md',
    'PLANNING_ARTIFACT_INDEX.md',
    'OPERATIONAL_VALIDATION_LOG.md',
    'PROCESS_TODO_ARCHIVE_STRATEGY.md',
    'CODE_PLACEMENT_POLICY.md',
    'COMMAND_NUMBER_SHORTCUTS.md',
    'TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md'
)

foreach ($processFile in $processCanonicalFiles) {
    $absolutePath = Join-Path (Join-Path $RepoRoot 'docs/process') $processFile
    if (Test-Path $absolutePath) {
        $markdownFiles += Get-Item $absolutePath
    }
}

$activeTodoDir = Join-Path $RepoRoot 'docs/process'
if (Test-Path $activeTodoDir) {
    $markdownFiles += Get-ChildItem -Path $activeTodoDir -File -Filter 'TODO_*.md'
}

$adrDir = Join-Path $RepoRoot 'docs/adr'
if (Test-Path $adrDir) {
    $markdownFiles += Get-ChildItem -Path $adrDir -File -Filter '*.md'
}

$referensiReadme = Join-Path $RepoRoot 'docs/referensi/README.md'
if (Test-Path $referensiReadme) {
    $markdownFiles += Get-Item $referensiReadme
}

$referensiLocalReadme = Join-Path $RepoRoot 'docs/referensi/_local/README.md'
if (Test-Path $referensiLocalReadme) {
    $markdownFiles += Get-Item $referensiLocalReadme
}

$markdownFiles = $markdownFiles |
    Where-Object {
        $_.FullName -notmatch '\\(archive|logs)\\'
    } |
    Sort-Object FullName -Unique

$errors = New-Object System.Collections.Generic.List[string]
$summaries = New-Object System.Collections.Generic.List[string]
$backtickPattern = '`(?<target>[^`]+)`'
$linkPattern = '\[[^\]]+\]\((?<target>[^)]+)\)'
$tokenPattern = '(?<![A-Za-z0-9_])(?<target>(?:AGENTS\.md|README\.md|PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X\.md|AI_[A-Z0-9_]+\.md|MARKDOWN_CONTEXT_SPACE_BUDGET\.md|OPERATIONAL_VALIDATION_LOG\.md|PLANNING_ARTIFACT_INDEX\.md|PROCESS_TODO_ARCHIVE_STRATEGY\.md|COMMAND_NUMBER_SHORTCUTS\.md|CODE_PLACEMENT_POLICY\.md|ADR_[0-9]{4}_[A-Z0-9_]+\.md|TODO_[A-Z0-9]{4,8}_[A-Z0-9_]+_\d{4}_\d{2}_\d{2}\.md|docs\/[^\s`|)]+|scripts\/[^\s`|)]+|\.github\/[^\s`|)]+))(?![A-Za-z0-9_])'

foreach ($file in $markdownFiles) {
    $lineNumber = 0
    foreach ($line in (Get-Content -Path $file.FullName)) {
        $lineNumber++

        foreach ($match in [regex]::Matches($line, $backtickPattern)) {
            $token = ([string]$match.Groups['target'].Value).Trim()
            if ($token.Length -gt 0) {
                Add-MissingReference -Errors $errors -RepoRoot $RepoRoot -SourceFile $file.FullName -LineNumber $lineNumber -Token $token
            }
        }

        foreach ($match in [regex]::Matches($line, $linkPattern)) {
            $token = ([string]$match.Groups['target'].Value).Trim()
            if ($token.Length -gt 0) {
                Add-MissingReference -Errors $errors -RepoRoot $RepoRoot -SourceFile $file.FullName -LineNumber $lineNumber -Token $token
            }
        }

        $lineWithoutBackticks = [regex]::Replace($line, $backtickPattern, ' ')
        foreach ($match in [regex]::Matches($lineWithoutBackticks, $tokenPattern)) {
            $token = ([string]$match.Groups['target'].Value).Trim()
            if ($token.Length -gt 0) {
                Add-MissingReference -Errors $errors -RepoRoot $RepoRoot -SourceFile $file.FullName -LineNumber $lineNumber -Token $token
            }
        }
    }
}

$summaries.Add("Markdown files scanned: $($markdownFiles.Count)")
$summaries.Add("Missing references: $($errors.Count)")

if (-not $Quiet) {
    Write-Output 'Markdown path audit summary:'
    foreach ($summary in $summaries) {
        Write-Output "  - $summary"
    }
}

if ($errors.Count -gt 0) {
    foreach ($errorMessage in ($errors | Sort-Object -Unique)) {
        Write-Output "ERROR: $errorMessage"
    }
    exit 1
}

if (-not $Quiet) {
    Write-Output 'Markdown path audit: PASS'
}
