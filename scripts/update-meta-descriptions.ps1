$map = @{
  'index.html' = 'Movimiento ciudadano por la libertad, transparencia y participación en Uruguay. Únete para impulsar un país más libre y próspero.'
  'documentos.html' = 'Consulta documentos oficiales, informes y propuestas de la Plataforma Por la Libertad en Uruguay.'
  'quienes-somos.html' = 'Conoce quiénes somos, nuestra misión y valores en la Plataforma Por la Libertad.'
  'propuestas.html' = 'Propuestas para un Uruguay más libre, transparente y próspero.'
  'noticias.html' = 'Noticias y novedades de la Plataforma Por la Libertad en Uruguay.'
  'contacto.html' = 'Canales oficiales para contactarnos. Envía tu consulta o propuesta.'
  'registro.html' = 'Forma parte de la Plataforma Por la Libertad. Regístrate y participa.'
  'politica-privacidad.html' = 'Política de Privacidad de la Plataforma Por la Libertad. Conoce cómo protegemos tus datos personales.'
  'terminos-condiciones.html' = 'Términos y condiciones de uso del sitio web de la Plataforma Por la Libertad.'
  'politicas-cookies.html' = 'Conoce cómo utilizamos cookies y cómo puedes gestionarlas.'
}

$files = Get-ChildItem -Path 'E:\PPLuy' -Filter *.html -File
foreach ($file in $files) {
  if (-not $map.ContainsKey($file.Name)) { continue }
  $text = Get-Content -Raw -Path $file.FullName
  $orig = $text
  $desc = $map[$file.Name]

  # name="description"
  $text = [Regex]::Replace($text, '(<meta[^>]+name="description"[^>]+content=")([^"\n]+)(")', "$1$desc$3", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  # og:description
  $text = [Regex]::Replace($text, '(<meta[^>]+property="og:description"[^>]+content=")([^"\n]+)(")', "$1$desc$3", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  # twitter:description
  $text = [Regex]::Replace($text, '(<meta[^>]+(?:name|property)="twitter:description"[^>]+content=")([^"\n]+)(")', "$1$desc$3", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)

  if ($text -ne $orig) {
    Set-Content -Path $file.FullName -Value $text -Encoding UTF8
    Write-Output ("Updated descriptions: {0}" -f $file.Name)
  }
}