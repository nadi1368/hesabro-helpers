# Hesabro Helpers

## Getting started

To install this package from GitLab will need a Personal Access Token. You can issue your PAT by going to Profile Icon > Edit Profile > Access Tokens. Then to issue a new PAT, write a name and select a scope `api/read_api`, `read_api` is a minimum. api will work too. If you want to set an expiry, can put value in that. Then press the Create personal access token Button.

![Gitlab Personal Access Token](https://hesabro-assets.s3.ir-thr-at1.arvanstorage.ir/gitlab-pat.webp?versionId=)

Next, from the terminal. Run the following command.

```shell
composer config --global --auth gitlab-token.gitlab.com PAT_TOKEN
```

Replace the `PAT_TOKEN` with the token you generated from first step.

Then, in your `composer.json` file, add the following snippet.

```json
"repositories": [
    {
        "type": "vcs",
        "url": "git@git.abrsa.ir:hesabro/backend-modules/hesabro-helpers.git"
    }
]
```

Finally,

```shell
composer require hesabro/helpers
```

Last version is: `v1.0.0`
