# GrumPHP Tasks

Requires NPM packages too!

Example package.json
```json
{
  "dependencies": {
    "eslint": "^4.1.1",
    "eslint-config-standard": "^10.2.1",
    "eslint-plugin-promise": "^3.5.0",
    "eslint-plugin-standard": "^3.0.1"
  }
}
```



Example `grumphp.yml`

```yaml
parameters:
  git_dir: .
  bin_dir: vendor/bin
  tasks:
    eslint: ~
services:
  task.eslint:
    class: Stickee\GrumPHP\ESLint
    arguments:
      - '@config'
      - '@process_builder'
      - '@formatter.raw_process'
    tags:
      - {name: grumphp.task, config: eslint}

```
