param([string]$Referencia = 'C:\Workspace\desarrollo-skc\control-servicios-comerciales')
$ErrorActionPreference = 'Stop'
$repo = Split-Path $PSScriptRoot -Parent
$files = @('public/assets/css/tailwind.css', 'resources/css/tailwind.css', 'tailwind.config.cjs')
foreach ($file in $files) {
    if (!(Test-Path -LiteralPath (Join-Path $Referencia $file))) { throw "Falta $file en la referencia." }
}
Copy-Item -LiteralPath (Join-Path $Referencia 'public/assets/css/tailwind.css') -Destination (Join-Path $repo 'public/assets/css/tailwind.css')
Copy-Item -LiteralPath (Join-Path $Referencia 'resources/css/tailwind.css') -Destination (Join-Path $repo 'resources/css/tailwind.css')
Copy-Item -LiteralPath (Join-Path $Referencia 'tailwind.config.cjs') -Destination (Join-Path $repo 'resources/theme/comerciales.cjs')
Write-Host 'Tema copiado. Ejecuta npm ci y npm run build:css para incluir todas las clases locales. Revisa git diff antes del commit.'
