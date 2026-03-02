param(
    [Parameter(Mandatory = $true)]
    [ValidatePattern('^[A-Z0-9]{4,8}$')]
    [string]$Code,

    [Parameter(Mandatory = $true)]
    [string]$Title,

    [string]$Date = (Get-Date).ToString('yyyy-MM-dd'),
    [string]$RelatedAdr = '-',
    [string]$OutputDir = 'docs/process',
    [switch]$DryRun,
    [switch]$Force
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

if ([string]::IsNullOrWhiteSpace($Title)) {
    throw 'Title wajib diisi.'
}

$parsedDate = [DateTime]::ParseExact($Date, 'yyyy-MM-dd', [System.Globalization.CultureInfo]::InvariantCulture)
$cleanTitle = $Title.Trim()
$dateToken = $parsedDate.ToString('yyyy_MM_dd')
$summaryToken = ($cleanTitle.ToUpperInvariant() -replace '[^A-Z0-9]+', '_').Trim('_')
if ([string]::IsNullOrWhiteSpace($summaryToken)) {
    $summaryToken = 'UNTITLED'
}

$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$templatePath = Join-Path $repoRoot 'docs/process/TEMPLATE_TODO_CONCERN.md'
if (-not (Test-Path $templatePath)) {
    throw "Template tidak ditemukan: $templatePath"
}

$outputDirectory = Join-Path $repoRoot $OutputDir
if (-not (Test-Path $outputDirectory)) {
    New-Item -Path $outputDirectory -ItemType Directory | Out-Null
}

$fileName = "TODO_$Code" + "_$summaryToken" + "_$dateToken.md"
$outputPath = Join-Path $outputDirectory $fileName

$templateContent = Get-Content -Path $templatePath -Raw
$resolvedAdr = if ([string]::IsNullOrWhiteSpace($RelatedAdr)) { '-' } else { $RelatedAdr.Trim() }

$content = $templateContent
$content = [regex]::Replace($content, '(?m)^# TODO <KODE_UNIK> <Judul Ringkas>$', "# TODO $Code $cleanTitle")
$content = [regex]::Replace($content, '(?m)^Tanggal:\s*YYYY-MM-DD[ \t]*$', "Tanggal: $($parsedDate.ToString('yyyy-MM-dd'))  ")
$content = [regex]::Replace($content, '(?m)^Related ADR:\s*`[^`]*`[ \t]*$', "Related ADR: ``$resolvedAdr``")

if ($DryRun) {
    Write-Output "Dry run:"
    Write-Output "  Template   : $templatePath"
    Write-Output "  Output file: $outputPath"
    Write-Output "  Title line : # TODO $Code $cleanTitle"
    Write-Output "  Date       : $($parsedDate.ToString('yyyy-MM-dd'))"
    Write-Output "  Related ADR: $resolvedAdr"
    return
}

if ((Test-Path $outputPath) -and -not $Force) {
    throw "File sudah ada: $outputPath. Gunakan -Force jika ingin overwrite."
}

$utf8NoBom = New-Object System.Text.UTF8Encoding($false)
[System.IO.File]::WriteAllText($outputPath, $content, $utf8NoBom)

Write-Output "TODO generated: $outputPath"
