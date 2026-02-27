param(
  [string]$OutputPath = "public/chart-pdf-examples/output/quickchart-example.pdf"
)

$outputFullPath = Resolve-Path -Path "." | ForEach-Object { Join-Path $_ $OutputPath }
$outputDir = Split-Path -Parent $outputFullPath

if (-not (Test-Path -Path $outputDir)) {
  New-Item -Path $outputDir -ItemType Directory | Out-Null
}

$chartConfig = @"
{
  "type": "bar",
  "data": {
    "labels": ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun"],
    "datasets": [{
      "label": "Kegiatan",
      "backgroundColor": "#2563eb",
      "data": [12, 19, 9, 14, 8, 11]
    }]
  },
  "options": {
    "plugins": {
      "title": {
        "display": true,
        "text": "QuickChart PDF Example"
      }
    }
  }
}
"@

$encodedConfig = [System.Uri]::EscapeDataString($chartConfig)
$url = "https://quickchart.io/chart?format=pdf&width=1200&height=700&c=$encodedConfig"

Invoke-WebRequest -Uri $url -OutFile $outputFullPath
Write-Host "PDF generated: $outputFullPath"
