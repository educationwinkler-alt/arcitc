param(
    [Parameter(Position = 0)]
    [ValidateSet("start", "install", "status", "stop", "restart", "logs")]
    [string] $Command = "status"
)

$ErrorActionPreference = "Stop"
$PSNativeCommandUseErrorActionPreference = $false
$root = Resolve-Path (Join-Path (Split-Path -Parent $MyInvocation.MyCommand.Path) "..")

function Invoke-DockerCompose {
    param(
        [Parameter(ValueFromRemainingArguments = $true)]
        [string[]] $Arguments
    )

    & docker compose @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw "docker compose $($Arguments -join ' ') failed with exit code $LASTEXITCODE"
    }
}

function Ensure-UploadsWritable {
    Invoke-DockerCompose exec wordpress sh -lc "mkdir -p wp-content/uploads && chown -R www-data:www-data wp-content/uploads && chmod -R 777 wp-content/uploads"
}

Push-Location $root
try {
    switch ($Command) {
        "start" {
            Invoke-DockerCompose up -d db wordpress adminer
            Ensure-UploadsWritable
        }
        "install" {
            Ensure-UploadsWritable

            & docker compose run --rm wpcli wp core is-installed
            if ($LASTEXITCODE -eq 0) {
                Write-Host "WordPress is already installed."
            } else {
                Invoke-DockerCompose run --rm wpcli wp core install `
                    --url="http://localhost:8090" `
                    --title="Arctic Spas Local" `
                    --admin_user="admin" `
                    --admin_password="admin" `
                    --admin_email="admin@example.test" `
                    --skip-email
            }

            Invoke-DockerCompose run --rm wpcli wp theme activate arctic
            Invoke-DockerCompose run --rm wpcli wp option update permalink_structure "/%postname%/"
            Invoke-DockerCompose run --rm wpcli wp rewrite flush
        }
        "status" {
            Invoke-DockerCompose ps
        }
        "stop" {
            Invoke-DockerCompose stop
        }
        "restart" {
            Invoke-DockerCompose restart wordpress
            Ensure-UploadsWritable
        }
        "logs" {
            Invoke-DockerCompose logs --tail=120 wordpress
        }
    }
} finally {
    Pop-Location
}
