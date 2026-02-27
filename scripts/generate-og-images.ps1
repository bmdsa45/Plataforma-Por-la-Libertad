Add-Type -AssemblyName System.Drawing

function Resize-Crop {
  param(
    [Parameter(Mandatory=$true)][string]$InputPath,
    [Parameter(Mandatory=$true)][string]$OutputPath,
    [Parameter(Mandatory=$true)][int]$TargetWidth,
    [Parameter(Mandatory=$true)][int]$TargetHeight,
    [int]$Quality = 85
  )
  if (!(Test-Path $InputPath)) { Write-Error "Input not found: $InputPath"; return }
  $src = [System.Drawing.Image]::FromFile($InputPath)
  try {
    $scale = [Math]::Max($TargetWidth / $src.Width, $TargetHeight / $src.Height)
    $newWidth = [int][Math]::Ceiling($src.Width * $scale)
    $newHeight = [int][Math]::Ceiling($src.Height * $scale)

    $bmp = New-Object System.Drawing.Bitmap $newWidth, $newHeight
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $g.CompositingQuality = [System.Drawing.Drawing2D.CompositingQuality]::HighQuality
    $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
    $g.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
    $g.DrawImage($src, 0, 0, $newWidth, $newHeight)

    $x = [int](($newWidth - $TargetWidth) / 2)
    $y = [int](($newHeight - $TargetHeight) / 2)
    $rect = New-Object System.Drawing.Rectangle $x, $y, $TargetWidth, $TargetHeight
    $crop = $bmp.Clone($rect, $bmp.PixelFormat)

    $jpegEncoder = [System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders() | Where-Object { $_.MimeType -eq 'image/jpeg' }
    $encParams = New-Object System.Drawing.Imaging.EncoderParameters 1
    $encParam = New-Object System.Drawing.Imaging.EncoderParameter ([System.Drawing.Imaging.Encoder]::Quality), $Quality
    $encParams.Param[0] = $encParam

    $dir = Split-Path -Parent $OutputPath
    if (!(Test-Path $dir)) { New-Item -ItemType Directory -Force -Path $dir | Out-Null }
    $crop.Save($OutputPath, $jpegEncoder, $encParams)

    $g.Dispose(); $bmp.Dispose(); $crop.Dispose()
    Write-Output "Saved: $OutputPath"
  } finally {
    $src.Dispose()
  }
}

# Generate OG/Twitter images from ppl.jpg
Resize-Crop -InputPath 'E:\PPLuy\ppl.jpg' -OutputPath 'E:\PPLuy\img\og\ppl-1200x630.jpg' -TargetWidth 1200 -TargetHeight 630 -Quality 85
Resize-Crop -InputPath 'E:\PPLuy\ppl.jpg' -OutputPath 'E:\PPLuy\img\og\ppl-600x315.jpg' -TargetWidth 600 -TargetHeight 315 -Quality 80