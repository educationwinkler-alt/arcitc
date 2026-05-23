param(
	[string]$EnvPath = ".\.env.local",
	[string]$OutDir = ".\docs"
)

$ErrorActionPreference = "Stop"

$envFile = Resolve-Path -LiteralPath $EnvPath
$tokenLine = Get-Content -LiteralPath $envFile | Where-Object { $_ -match "^FIGMA_TOKEN=" } | Select-Object -First 1

if (-not $tokenLine) {
	throw "FIGMA_TOKEN not found in $EnvPath"
}

$token = $tokenLine -replace "^FIGMA_TOKEN=", ""

if ([string]::IsNullOrWhiteSpace($token)) {
	throw "FIGMA_TOKEN is empty"
}

$headers = @{ "X-Figma-Token" = $token }
$files = @(
	@{ label = "wireframe"; key = "puPBNFpuaXpRZR2TINaDvm" },
	@{ label = "grafika"; key = "xeOew3dFjDVfjXZrJ09emM" }
)

New-Item -ItemType Directory -Force -Path $OutDir | Out-Null

$summary = @()
$nodes = @()

foreach ($file in $files) {
	$data = Invoke-RestMethod -Uri "https://api.figma.com/v1/files/$($file.key)?depth=2" -Headers $headers -Method Get -TimeoutSec 60

	$pages = @($data.document.children | ForEach-Object {
		[pscustomobject]@{
			name = $_.name
			type = $_.type
			child_count = @($_.children).Count
			children = @($_.children | Select-Object -First 50 | ForEach-Object {
				[pscustomobject]@{
					name = $_.name
					type = $_.type
					id = $_.id
				}
			})
		}
	})

	$summary += [pscustomobject]@{
		label = $file.label
		key = $file.key
		name = $data.name
		lastModified = $data.lastModified
		thumbnailUrl = $data.thumbnailUrl
		pages = $pages
	}

	foreach ($page in $data.document.children) {
		foreach ($node in $page.children) {
			$box = $node.absoluteBoundingBox
			$nodes += [pscustomobject]@{
				file = $file.label
				file_name = $data.name
				page = $page.name
				id = $node.id
				type = $node.type
				name = $node.name
				x = if ($box) { [math]::Round($box.x, 2) } else { $null }
				y = if ($box) { [math]::Round($box.y, 2) } else { $null }
				width = if ($box) { [math]::Round($box.width, 2) } else { $null }
				height = if ($box) { [math]::Round($box.height, 2) } else { $null }
			}
		}
	}
}

$summary | ConvertTo-Json -Depth 8 | Set-Content -LiteralPath (Join-Path $OutDir "figma-api-summary.json") -Encoding UTF8
$nodes | ConvertTo-Json -Depth 5 | Set-Content -LiteralPath (Join-Path $OutDir "figma-top-level-nodes.json") -Encoding UTF8

$summary | ForEach-Object {
	[pscustomobject]@{
		Label = $_.label
		Name = $_.name
		LastModified = $_.lastModified
		Pages = ($_.pages.name -join ", ")
	}
} | Format-Table -AutoSize
