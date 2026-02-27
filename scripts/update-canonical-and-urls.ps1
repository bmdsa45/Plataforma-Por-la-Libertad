$base = 'https://bmdsa45.github.io/Plataforma-Por-la-Libertad/'
$files = Get-ChildItem -Path 'E:\PPLuy' -Filter *.html -File
foreach ($file in $files) {
  $text = Get-Content -Raw -Path $file.FullName
  $orig = $text
  if ($file.Name -eq 'index.html') { $pageUrl = $base } else { $pageUrl = $base + $file.Name }

  # canonical
  $text = [Regex]::Replace($text, '(<link[^>]+rel="canonical"[^>]+href=")([^"\n]+)(")', "$1$pageUrl$3", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  # og:url
  $text = [Regex]::Replace($text, '(<meta[^>]+property="og:url"[^>]+content=")([^"\n]+)(")', "$1$pageUrl$3", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  # twitter:url
  $text = [Regex]::Replace($text, '(<meta[^>]+(?:name|property)="twitter:url"[^>]+content=")([^"\n]+)(")', "$1$pageUrl$3", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  # JSON-LD url
  $text = [Regex]::Replace($text, '("url"\s*:\s*")([^"]+)(")', "$1$pageUrl$3", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  # JSON-LD logo -> ppluy.jpg
  $logo = $base + 'ppluy.jpg'
  $text = [Regex]::Replace($text, '("logo"\s*:\s*")([^"]+)(")', "$1$logo$3", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  # twitter:site -> @PPLuy_
  $text = [Regex]::Replace($text, '(<meta[^>]+(?:name|property)="twitter:site"[^>]+content=")([^"\n]+)(")', '$1@PPLuy_$3', [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  # lang attribute -> es-UY
  $text = [Regex]::Replace($text, '(<html[^>]*\s)lang="[^"]+"', '$1lang="es-UY"', [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  # og:locale -> es_UY
  $text = [Regex]::Replace($text, '(<meta[^>]+property="og:locale"[^>]+content=")([^"\n]+)(")', '$1es_UY$3', [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)

  if ($text -ne $orig) {
    Set-Content -Path $file.FullName -Value $text -Encoding UTF8
    Write-Output ("Updated URLs: {0}" -f $file.Name)
  }
}