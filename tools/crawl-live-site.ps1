param(
    [string] $BaseUrl = "https://www.arctic-spas.cz/",
    [string] $OutputDir = "docs/crawl-live",
    [int] $MaxPages = 500
)

$ErrorActionPreference = "Stop"
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$rootDir = Resolve-Path (Join-Path $scriptDir "..")
$resolvedOutputDir = Join-Path $rootDir $OutputDir
New-Item -ItemType Directory -Force -Path $resolvedOutputDir | Out-Null

$base = [Uri]$BaseUrl
$userAgent = "ArcticSpasMigrationCrawler/1.0"

function ConvertTo-SafeText {
    param([string] $Html)
    if ([string]::IsNullOrWhiteSpace($Html)) {
        return ""
    }
    $text = $Html -replace "<script[\s\S]*?</script>", " " -replace "<style[\s\S]*?</style>", " "
    $text = $text -replace "<[^>]+>", " "
    $text = [System.Net.WebUtility]::HtmlDecode($text)
    $text = $text -replace "\s+", " "
    return $text.Trim()
}

function Get-HttpText {
    param([string] $Url)

    $request = [System.Net.HttpWebRequest]::Create($Url)
    $request.UserAgent = $userAgent
    $request.AllowAutoRedirect = $true
    $request.Timeout = 20000
    $request.ReadWriteTimeout = 20000

    try {
        $response = [System.Net.HttpWebResponse]$request.GetResponse()
        $status = [int]$response.StatusCode
        $finalUrl = $response.ResponseUri.AbsoluteUri
        $contentType = $response.ContentType
        $charset = $response.CharacterSet
        if ($contentType -match "text/html|text/xml|application/xml" -or $Url -match "\.php($|\?)|/$") {
            $charset = "utf-8"
        }
        if ([string]::IsNullOrWhiteSpace($charset)) {
            $charset = "utf-8"
        }

        $memory = New-Object System.IO.MemoryStream
        $stream = $response.GetResponseStream()
        $stream.CopyTo($memory)
        $bytes = $memory.ToArray()
        $stream.Close()
        $response.Close()

        $encoding = [System.Text.Encoding]::GetEncoding($charset)
        $text = $encoding.GetString($bytes)

        return [PSCustomObject]@{
            Url = $Url
            FinalUrl = $finalUrl
            Status = $status
            ContentType = $contentType
            Text = $text
            Error = ""
        }
    } catch [System.Net.WebException] {
        $status = 0
        $contentType = ""
        $text = ""
        if ($_.Exception.Response) {
            $response = [System.Net.HttpWebResponse]$_.Exception.Response
            $status = [int]$response.StatusCode
            $contentType = $response.ContentType
            $response.Close()
        }
        return [PSCustomObject]@{
            Url = $Url
            FinalUrl = $Url
            Status = $status
            ContentType = $contentType
            Text = ""
            Error = $_.Exception.Message
        }
    }
}

function Normalize-Url {
    param([string] $Href, [Uri] $Context)

    if ([string]::IsNullOrWhiteSpace($Href)) {
        return $null
    }
    if ($Href.StartsWith("mailto:") -or $Href.StartsWith("tel:") -or $Href.StartsWith("javascript:")) {
        return $null
    }

    try {
        $uri = [Uri]::new($Context, $Href)
    } catch {
        return $null
    }

    if ($uri.Host -ne $base.Host) {
        return $null
    }

    $builder = [UriBuilder]$uri
    $builder.Fragment = ""
    $builder.Query = ""
    $normalized = $builder.Uri.AbsoluteUri

    $ext = [IO.Path]::GetExtension($builder.Path).ToLowerInvariant()
    $skip = @(".jpg", ".jpeg", ".png", ".gif", ".webp", ".svg", ".ico", ".css", ".js", ".xml")
    if ($skip -contains $ext) {
        return $null
    }

    return $normalized
}

function Get-SitemapUrls {
    param([string] $SitemapUrl)

    $result = New-Object System.Collections.Generic.List[string]
    $response = Get-HttpText -Url $SitemapUrl
    if ($response.Status -ne 200 -or [string]::IsNullOrWhiteSpace($response.Text)) {
        return $result
    }

    [xml]$xml = $response.Text
    if ($xml.sitemapindex) {
        foreach ($sitemap in $xml.sitemapindex.sitemap) {
            foreach ($url in Get-SitemapUrls -SitemapUrl $sitemap.loc) {
                $result.Add($url)
            }
        }
    } elseif ($xml.urlset) {
        foreach ($url in $xml.urlset.url) {
            $normalized = Normalize-Url -Href $url.loc -Context $base
            if ($normalized) {
                $result.Add($normalized)
            }
        }
    }
    return $result
}

function Get-InternalLinks {
    param([string] $Html, [string] $PageUrl)

    $links = New-Object System.Collections.Generic.List[string]
    if ([string]::IsNullOrWhiteSpace($Html)) {
        return $links
    }

    $context = [Uri]$PageUrl
    $matches = [regex]::Matches($Html, "href\s*=\s*[""']([^""']+)[""']", "IgnoreCase")
    foreach ($match in $matches) {
        $normalized = Normalize-Url -Href $match.Groups[1].Value -Context $context
        if ($normalized) {
            $links.Add($normalized)
        }
    }
    return $links
}

function Get-FirstMatchText {
    param([string] $Html, [string] $Pattern)
    $match = [regex]::Match($Html, $Pattern, "IgnoreCase,Singleline")
    if (-not $match.Success) {
        return ""
    }
    return ConvertTo-SafeText $match.Groups[1].Value
}

function Get-MigrationDecision {
    param([string] $Url, [string] $Title, [string] $H1)

    $path = ([Uri]$Url).AbsolutePath.TrimStart("/").ToLowerInvariant()
    if ([string]::IsNullOrWhiteSpace($path)) {
        $path = "index"
    }

    $retiredRedirects = @{
        "virivky-dreammaker.php" = "/virivky/"
        "virivka-ellesmere.php"  = "/virivky/"
        "virivka-aurora.php"     = "/virivky/"
        "virivka-orca.php"       = "/virivky/"
        "virivka-grizzly.php"    = "/virivky/"
    }
    if ($retiredRedirects.ContainsKey($path)) {
        return [PSCustomObject]@{
            Type = "retired"
            Action = "redirect_only"
            Target = $retiredRedirects[$path]
            Note = "Nemigrovat jako aktivni produkt podle briefu; zachytit kvuli SEO/UX."
        }
    }

    $activeHotTubs = @(
        "virivka-lunar.php", "virivka-orion.php", "virivka-husky.php",
        "virivka-timberwolf.php", "virivka-eagle.php", "virivka-totem.php", "virivka-mustang.php", "virivka-mckinley.php",
        "virivka-fox.php", "virivka-cub.php", "virivka-yukon.php", "virivka-klondiker.php", "virivka-kodiak.php",
        "virivka-tundra.php", "virivka-summit.php", "virivka-summit-xl.php"
    )
    if ($activeHotTubs -contains $path) {
        return [PSCustomObject]@{
            Type = "product_hot_tub"
            Action = "migrate_product"
            Target = ""
            Note = "Aktivni virivka."
        }
    }

    $activeSwimspa = @(
        "bazen-ocean.php", "bazen-okanagan.php", "bazen-hudson.php", "bazen-kingfisher.php", "bazen-wolverine.php", "bazen-athabascan.php"
    )
    if ($activeSwimspa -contains $path) {
        return [PSCustomObject]@{
            Type = "product_swimspa"
            Action = "migrate_product"
            Target = ""
            Note = "Aktivni swimspa."
        }
    }

    $otherSortiment = @("covana.php", "sauny.php", "koupaci-sudy-kirami.php", "prislusenstvi-doplnky.php")
    if ($otherSortiment -contains $path) {
        return [PSCustomObject]@{
            Type = "product_other_sortiment"
            Action = "review_product_or_external"
            Target = ""
            Note = "Sirsi sortiment; overit full detail vs. landing/external shop."
        }
    }

    if ($path.StartsWith("content/download/") -or $path.EndsWith(".pdf") -or $path.EndsWith(".doc") -or $path.EndsWith(".docx")) {
        return [PSCustomObject]@{
            Type = "download_asset"
            Action = "review_download"
            Target = ""
            Note = "Soubor ke stazeni; rozhodnout CPT download vs. repeater."
        }
    }

    if ($path -eq "index" -or $path -eq "index.php") {
        return [PSCustomObject]@{
            Type = "home"
            Action = "migrate_page"
            Target = "/"
            Note = "Homepage."
        }
    }

    return [PSCustomObject]@{
        Type = "content_page"
        Action = "review_migrate_page"
        Target = ""
        Note = "Obsahova stranka; klasifikovat v migracni mape."
    }
}

$queue = New-Object System.Collections.Queue
$seen = @{}
$rows = New-Object System.Collections.Generic.List[object]

$sitemapUrl = (New-Object Uri($base, "sitemap.xml")).AbsoluteUri
$seedUrls = New-Object System.Collections.Generic.List[string]
$seedUrls.Add($base.AbsoluteUri)
foreach ($url in Get-SitemapUrls -SitemapUrl $sitemapUrl) {
    $seedUrls.Add($url)
}

foreach ($url in ($seedUrls | Select-Object -Unique)) {
    $queue.Enqueue($url)
    $seen[$url] = $true
}

while ($queue.Count -gt 0 -and $rows.Count -lt $MaxPages) {
    $url = [string]$queue.Dequeue()
    Write-Host ("Crawling {0}" -f $url)

    $response = Get-HttpText -Url $url
    $html = $response.Text
    $title = Get-FirstMatchText -Html $html -Pattern "<title[^>]*>(.*?)</title>"
    $h1 = Get-FirstMatchText -Html $html -Pattern "<h1[^>]*>(.*?)</h1>"
    $description = ""
    $metaDescription = [regex]::Match($html, "<meta[^>]+name\s*=\s*[""']description[""'][^>]+content\s*=\s*[""']([^""']*)[""']", "IgnoreCase,Singleline")
    if ($metaDescription.Success) {
        $description = [System.Net.WebUtility]::HtmlDecode($metaDescription.Groups[1].Value).Trim()
    }

    $decision = Get-MigrationDecision -Url $url -Title $title -H1 $h1
    $internalLinks = @()
    if ($response.ContentType -match "html") {
        $internalLinks = @(Get-InternalLinks -Html $html -PageUrl $response.FinalUrl)
        foreach ($link in $internalLinks) {
            if (-not $seen.ContainsKey($link) -and $rows.Count + $queue.Count -lt $MaxPages) {
                $seen[$link] = $true
                $queue.Enqueue($link)
            }
        }
    }

    $rows.Add([PSCustomObject]@{
        Url = $url
        FinalUrl = $response.FinalUrl
        Status = $response.Status
        ContentType = $response.ContentType
        Title = $title
        H1 = $h1
        MetaDescription = $description
        Type = $decision.Type
        MigrationAction = $decision.Action
        SuggestedTarget = $decision.Target
        Note = $decision.Note
        InternalLinkCount = $internalLinks.Count
        Error = $response.Error
    })

    Start-Sleep -Milliseconds 120
}

$timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
$csvPath = Join-Path $resolvedOutputDir "arctic-spas-live-crawl.csv"
$jsonPath = Join-Path $resolvedOutputDir "arctic-spas-live-crawl.json"
$mdPath = Join-Path $resolvedOutputDir "arctic-spas-live-crawl.md"

$rows | Sort-Object Url | Export-Csv -LiteralPath $csvPath -NoTypeInformation -Encoding UTF8
$rows | Sort-Object Url | ConvertTo-Json -Depth 5 | Set-Content -LiteralPath $jsonPath -Encoding UTF8

$byAction = $rows | Group-Object MigrationAction | Sort-Object Name
$byType = $rows | Group-Object Type | Sort-Object Name
$redirects = $rows | Where-Object { $_.MigrationAction -eq "redirect_only" } | Sort-Object Url
$review = $rows | Where-Object { $_.MigrationAction -like "review*" } | Sort-Object Url

$md = New-Object System.Collections.Generic.List[string]
$md.Add("# Crawl live webu Arctic Spas")
$md.Add("")
$md.Add("Datum: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')")
$md.Add(('Zdroj: `{0}`' -f $BaseUrl))
$md.Add("Pocet URL: $($rows.Count)")
$md.Add("")
$md.Add("## Souhrn podle akce")
$md.Add("")
$md.Add("| Akce | Pocet |")
$md.Add("| --- | ---: |")
foreach ($group in $byAction) {
    $md.Add(('| `{0}` | {1} |' -f $group.Name, $group.Count))
}
$md.Add("")
$md.Add("## Souhrn podle typu")
$md.Add("")
$md.Add("| Typ | Pocet |")
$md.Add("| --- | ---: |")
foreach ($group in $byType) {
    $md.Add(('| `{0}` | {1} |' -f $group.Name, $group.Count))
}
$md.Add("")
$md.Add("## Redirect-only URL")
$md.Add("")
foreach ($row in $redirects) {
    $md.Add(('- `{0}` -> `{1}`; {2}' -f $row.Url, $row.SuggestedTarget, $row.Note))
}
$md.Add("")
$md.Add("## URL k rucni kontrole")
$md.Add("")
foreach ($row in ($review | Select-Object -First 80)) {
    $md.Add(('- `{0}` - `{1}` - {2}' -f $row.Url, $row.MigrationAction, $row.Title))
}
$md.Add("")
$md.Add("## Vystupy")
$md.Add("")
$md.Add('- `arctic-spas-live-crawl.csv`')
$md.Add('- `arctic-spas-live-crawl.json`')

$md | Set-Content -LiteralPath $mdPath -Encoding UTF8

Write-Host "Saved:"
Write-Host $csvPath
Write-Host $jsonPath
Write-Host $mdPath
