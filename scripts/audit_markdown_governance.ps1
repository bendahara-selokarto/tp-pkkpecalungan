param(
    [string]$RepoRoot = '',
    [switch]$Quiet
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Get-EstimatedTokens {
    param(
        [Parameter(Mandatory = $true)]
        [long]$Chars
    )

    return [int][math]::Ceiling($Chars / 4)
}

function Get-TrimmedValue {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Value
    )

    return ($Value.Trim() -replace '`', '')
}

function Get-MarkdownTableRows {
    param(
        [Parameter(Mandatory = $true)]
        [object[]]$Lines,

        [Parameter(Mandatory = $true)]
        [string]$Heading
    )

    $normalizedLines = @($Lines | ForEach-Object { [string]$_ })
    $startIndex = [Array]::IndexOf($normalizedLines, $Heading)
    if ($startIndex -lt 0) {
        throw "Heading tidak ditemukan: $Heading"
    }

    $rows = @()
    $tableStarted = $false
    for ($i = $startIndex + 1; $i -lt $normalizedLines.Length; $i++) {
        $line = $normalizedLines[$i]
        if ([string]::IsNullOrWhiteSpace($line)) {
            if ($tableStarted) {
                break
            }
            continue
        }

        if (-not $line.TrimStart().StartsWith('|')) {
            if ($tableStarted) {
                break
            }
            continue
        }

        $tableStarted = $true
        $cells = $line.Trim().Trim('|').Split('|') | ForEach-Object { $_.Trim() }
        if ($cells.Count -lt 2) {
            continue
        }

        if ($cells[0] -eq '---' -or $cells[0] -eq 'Artefak' -or $cells[0] -eq 'Concern ID') {
            continue
        }

        $rows += ,$cells
    }

    return $rows
}

function Get-FileMetric {
    param(
        [Parameter(Mandatory = $true)]
        [string]$AbsolutePath
    )

    if (-not (Test-Path $AbsolutePath)) {
        throw "File tidak ditemukan: $AbsolutePath"
    }

    $chars = (Get-Item $AbsolutePath).Length
    return [pscustomobject]@{
        Path = $AbsolutePath
        Chars = [int]$chars
        EstimatedTokens = Get-EstimatedTokens -Chars $chars
    }
}

if ([string]::IsNullOrWhiteSpace($RepoRoot)) {
    $RepoRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
} else {
    $RepoRoot = (Resolve-Path $RepoRoot).Path
}

$budgetPath = Join-Path $RepoRoot 'docs/process/MARKDOWN_CONTEXT_SPACE_BUDGET.md'
$registryPath = Join-Path $RepoRoot 'docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md'
$validationLogPath = Join-Path $RepoRoot 'docs/process/OPERATIONAL_VALIDATION_LOG.md'
$singlePathPath = Join-Path $RepoRoot 'docs/process/AI_SINGLE_PATH_ARCHITECTURE.md'
$playbookPath = Join-Path $RepoRoot 'docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md'
$patternDetailsPath = Join-Path $RepoRoot 'docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md'
$agentsPath = Join-Path $RepoRoot 'AGENTS.md'
$adrExamplePath = Join-Path $RepoRoot 'docs/adr/ADR_0005_TAHUN_ANGGARAN_CONTEXT_ISOLATION.md'

$budgetLines = @(Get-Content -Path $budgetPath)
$registryLines = @(Get-Content -Path $registryPath)
$validationLogLines = @(Get-Content -Path $validationLogPath)

$softCapRows = Get-MarkdownTableRows -Lines $budgetLines -Heading '## Soft Cap per Artefak'
$softCaps = @{}
foreach ($row in $softCapRows) {
    $artifact = Get-TrimmedValue -Value $row[0]
    $softCaps[$artifact] = [int](($row[1] -replace '[^0-9]', ''))
}

$requiredArtifacts = @(
    'AGENTS.md',
    'AI_SINGLE_PATH_ARCHITECTURE.md',
    'AI_FRIENDLY_EXECUTION_PLAYBOOK.md',
    'TTM25R1 thin registry',
    'OPERATIONAL_VALIDATION_LOG.md',
    'TODO concern aktif',
    'ADR aktif',
    'PLAYBOOK_PATTERN_DETAILS annex'
)

foreach ($artifact in $requiredArtifacts) {
    if (-not $softCaps.ContainsKey($artifact)) {
        throw "Soft cap tidak ditemukan pada budget doc untuk artefak: $artifact"
    }
}

$errors = New-Object System.Collections.Generic.List[string]
$warnings = New-Object System.Collections.Generic.List[string]
$summaries = New-Object System.Collections.Generic.List[string]

$artifactFileMap = @(
    @{ Artifact = 'AGENTS.md'; Path = $agentsPath; ExactCap = $false },
    @{ Artifact = 'AI_SINGLE_PATH_ARCHITECTURE.md'; Path = $singlePathPath; ExactCap = $false },
    @{ Artifact = 'AI_FRIENDLY_EXECUTION_PLAYBOOK.md'; Path = $playbookPath; ExactCap = $false },
    @{ Artifact = 'TTM25R1 thin registry'; Path = $registryPath; ExactCap = $false },
    @{ Artifact = 'OPERATIONAL_VALIDATION_LOG.md'; Path = $validationLogPath; ExactCap = $false },
    @{ Artifact = 'PLAYBOOK_PATTERN_DETAILS annex'; Path = $patternDetailsPath; ExactCap = $true },
    @{ Artifact = 'ADR aktif'; Path = $adrExamplePath; ExactCap = $false }
)

foreach ($item in $artifactFileMap) {
    $metric = Get-FileMetric -AbsolutePath $item.Path
    $softCap = $softCaps[$item.Artifact]
    $threshold = if ($item.ExactCap) { $softCap } else { [int][math]::Floor($softCap * 1.1) }

    if ($metric.Chars -gt $threshold) {
        $errors.Add("$($item.Artifact) melewati ambang enforcement: $($metric.Chars) chars > $threshold chars.")
    } elseif ($metric.Chars -gt $softCap) {
        $warnings.Add("$($item.Artifact) melewati soft cap: $($metric.Chars) chars > $softCap chars.")
    }

    $summaries.Add("$($item.Artifact): $($metric.Chars) chars ($($metric.EstimatedTokens) est. tokens)")
}

$registryRows = Get-MarkdownTableRows -Lines $registryLines -Heading '## Registry Concern Aktif'
$activeTodoCap = $softCaps['TODO concern aktif']
$allowedStatuses = @('planned', 'in-progress')
$activeConcernPaths = New-Object System.Collections.Generic.List[string]

foreach ($row in $registryRows) {
    if ($row.Count -lt 5) {
        continue
    }

    $status = Get-TrimmedValue -Value $row[3]
    if ($allowedStatuses -notcontains $status) {
        $errors.Add("TTM25R1 memuat status non-aktif pada tabel concern aktif: $status")
    }

    $todoRelativePath = Get-TrimmedValue -Value $row[2]
    $todoAbsolutePath = Join-Path $RepoRoot $todoRelativePath
    $activeConcernPaths.Add($todoAbsolutePath)

    $metric = Get-FileMetric -AbsolutePath $todoAbsolutePath
    $todoThreshold = [int][math]::Floor($activeTodoCap * 1.1)
    if ($metric.Chars -gt $todoThreshold) {
        $errors.Add("TODO concern aktif melewati ambang enforcement: $todoRelativePath ($($metric.Chars) chars > $todoThreshold chars).")
    } elseif ($metric.Chars -gt $activeTodoCap) {
        $warnings.Add("TODO concern aktif melewati soft cap: $todoRelativePath ($($metric.Chars) chars > $activeTodoCap chars).")
    }
}

if (-not ($registryLines -contains '## Pointer Closure Terbaru')) {
    $errors.Add('TTM25R1 tidak memiliki section "Pointer Closure Terbaru".')
}

$validationSliceStart = [Array]::IndexOf($validationLogLines, '### Registry SOT (`TTM25R1`)')
$validationSliceEnd = [Array]::IndexOf($validationLogLines, '### Pointer Closure Terbaru')
if ($validationSliceStart -lt 0 -or $validationSliceEnd -lt 0 -or $validationSliceEnd -le $validationSliceStart) {
    $errors.Add('OPERATIONAL_VALIDATION_LOG.md tidak memiliki boundary section aktif yang valid.')
} else {
    $activeSlice = $validationLogLines[($validationSliceStart + 1)..($validationSliceEnd - 1)] -join "`n"
    if ($activeSlice -match '\(`done`') {
        $errors.Add('OPERATIONAL_VALIDATION_LOG.md masih memuat status `done` pada snapshot concern aktif.')
    }
}

$minimumRoutingTokens = (Get-EstimatedTokens -Chars (Get-Item $agentsPath).Length) +
    (Get-EstimatedTokens -Chars (Get-Item $registryPath).Length) +
    (Get-EstimatedTokens -Chars (Get-Item $validationLogPath).Length)

$childConcernPath = $null
$parentConcernPath = $null

foreach ($row in $registryRows) {
    $status = Get-TrimmedValue -Value $row[3]
    $todoRelativePath = Get-TrimmedValue -Value $row[2]
    $todoAbsolutePath = Join-Path $RepoRoot $todoRelativePath

    if ($status -eq 'in-progress' -and $null -eq $childConcernPath) {
        $childConcernPath = $todoAbsolutePath
    }

    if ($status -eq 'planned' -and $null -eq $parentConcernPath) {
        $parentConcernPath = $todoAbsolutePath
    }
}

if ($null -eq $childConcernPath -and $activeConcernPaths.Count -gt 0) {
    $childConcernPath = $activeConcernPaths[0]
}

if ($null -eq $parentConcernPath -and $activeConcernPaths.Count -gt 0) {
    $parentConcernPath = $activeConcernPaths[0]
}

$singlePathTokens = Get-EstimatedTokens -Chars (Get-Item $singlePathPath).Length
$playbookTokens = Get-EstimatedTokens -Chars (Get-Item $playbookPath).Length
$adrTokens = Get-EstimatedTokens -Chars (Get-Item $adrExamplePath).Length

if ($null -ne $childConcernPath) {
    $defaultChildPack = $minimumRoutingTokens + $singlePathTokens + (Get-EstimatedTokens -Chars (Get-Item $childConcernPath).Length)
    if ($defaultChildPack -gt 18000) {
        $errors.Add("Default execution pack (child concern) melewati trigger compaction: $defaultChildPack tokens.")
    }
    $summaries.Add("Default child pack: $defaultChildPack est. tokens")
}

if ($null -ne $parentConcernPath) {
    $defaultParentPack = $minimumRoutingTokens + $singlePathTokens + (Get-EstimatedTokens -Chars (Get-Item $parentConcernPath).Length)
    if ($defaultParentPack -gt 18000) {
        $errors.Add("Default execution pack (parent concern) melewati trigger compaction: $defaultParentPack tokens.")
    }
    $summaries.Add("Default parent pack: $defaultParentPack est. tokens")

    $extendedParentPack = $defaultParentPack + $playbookTokens + $adrTokens
    $summaries.Add("Extended parent+ADR pack: $extendedParentPack est. tokens")
}

if (-not $Quiet) {
    Write-Output 'Markdown governance audit summary:'
    foreach ($summary in $summaries) {
        Write-Output "  - $summary"
    }

    if ($warnings.Count -gt 0) {
        Write-Output 'Warnings:'
        foreach ($warning in $warnings) {
            Write-Output "  - $warning"
        }
    }
}

if ($errors.Count -gt 0) {
    foreach ($errorMessage in $errors) {
        Write-Error $errorMessage
    }
    exit 1
}

if (-not $Quiet) {
    Write-Output 'Markdown governance audit: PASS'
}
