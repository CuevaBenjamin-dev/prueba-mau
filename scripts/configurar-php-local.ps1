param(
    [Parameter(Mandatory = $true)]
    [string] $PhpExecutable
)

$ErrorActionPreference = 'Stop'
$phpPath = (Resolve-Path -LiteralPath $PhpExecutable).Path
$phpDirectory = Split-Path -Parent $phpPath
$extensionDirectory = (Join-Path $phpDirectory 'ext').Replace('\', '/')
$projectDirectory = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '..')).Path
$localDirectory = Join-Path $projectDirectory '.local'
$caPath = Join-Path $localDirectory 'cacert.pem'
$iniPath = Join-Path $localDirectory 'php.ini'

if (-not (Test-Path -LiteralPath (Join-Path $phpDirectory 'ext\php_curl.dll'))) {
    throw 'La instalación de PHP indicada no contiene la extensión cURL para Windows.'
}

New-Item -ItemType Directory -Force -Path $localDirectory | Out-Null
Invoke-WebRequest -Uri 'https://curl.se/ca/cacert.pem' -OutFile $caPath

if ((Get-Item -LiteralPath $caPath).Length -lt 10000 -or
    -not (Select-String -LiteralPath $caPath -Pattern '-----BEGIN CERTIFICATE-----' -Quiet)) {
    throw 'El paquete de certificados descargado no parece ser un archivo PEM válido.'
}

$caIniPath = $caPath.Replace('\', '/')
$lines = @(
    '; Configuración local generada para PHP 8.3 en Windows.'
    ('extension_dir="' + $extensionDirectory + '"')
    'extension=curl'
    'extension=fileinfo'
    'extension=mbstring'
    'extension=openssl'
    'extension=pdo_mysql'
    'extension=pdo_sqlite'
    'extension=sqlite3'
    'extension=zip'
    ('curl.cainfo="' + $caIniPath + '"')
    ('openssl.cafile="' + $caIniPath + '"')
)
$lines | Set-Content -LiteralPath $iniPath -Encoding utf8

$configuredCa = & $phpPath -c $iniPath -r 'echo ini_get("curl.cainfo");'
if ($LASTEXITCODE -ne 0 -or $configuredCa -ne $caIniPath) {
    throw 'PHP no cargó la ruta del paquete CA. Revisa el archivo php.ini generado.'
}

Write-Output "PHP configurado: $phpPath"
Write-Output "Certificados CA: $caPath"
Write-Output "Configuración local: $iniPath"
