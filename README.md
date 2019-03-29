# GrumPHP Tasks
[![All Contributors](https://img.shields.io/badge/all_contributors-6-orange.svg?style=flat-square)](#contributors)

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
    class: Mez\GrumPHP\ESLint
    arguments:
      - '@config'
      - '@process_builder'
      - '@formatter.raw_process'
    tags:
      - {name: grumphp.task, config: eslint}

```

## Installation

```
composer require mez/grumphp-tasks
```
and then either
```
yarn add eslint eslint-config-standard eslint-plugin-promise eslint-plugin-standard
```

or 

```
npm install --save eslint eslint-config-standard eslint-plugin-promise eslint-plugin-standard
```

## Contributors

Thanks goes to these wonderful people ([emoji key](https://github.com/all-contributors/all-contributors#emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore -->
<table><tr><td align="center"><a href="https://www.sourceguru.net"><img src="https://avatars3.githubusercontent.com/u/570639?v=4" width="100px;" alt="Martin Meredith"/><br /><sub><b>Martin Meredith</b></sub></a><br /><a href="https://github.com/Mezzle/grumphp-tasks/commits?author=mezzle" title="Code">💻</a> <a href="#review-mezzle" title="Reviewed Pull Requests">👀</a> <a href="#ideas-mezzle" title="Ideas, Planning, & Feedback">🤔</a> <a href="https://github.com/Mezzle/grumphp-tasks/commits?author=mezzle" title="Documentation">📖</a></td><td align="center"><a href="http://www.joltbox.co.uk"><img src="https://avatars2.githubusercontent.com/u/401928?v=4" width="100px;" alt="Chris Ivens"/><br /><sub><b>Chris Ivens</b></sub></a><br /><a href="https://github.com/Mezzle/grumphp-tasks/commits?author=chrisivens" title="Code">💻</a> <a href="#review-chrisivens" title="Reviewed Pull Requests">👀</a> <a href="#ideas-chrisivens" title="Ideas, Planning, & Feedback">🤔</a></td><td align="center"><a href="https://github.com/MarioSteinitz"><img src="https://avatars1.githubusercontent.com/u/11737051?v=4" width="100px;" alt="Mario Steinitz"/><br /><sub><b>Mario Steinitz</b></sub></a><br /><a href="https://github.com/Mezzle/grumphp-tasks/commits?author=MarioSteinitz" title="Code">💻</a></td><td align="center"><a href="http://simonhartcher.com"><img src="https://avatars3.githubusercontent.com/u/856194?v=4" width="100px;" alt="Simon Hartcher"/><br /><sub><b>Simon Hartcher</b></sub></a><br /><a href="https://github.com/Mezzle/grumphp-tasks/commits?author=deevus" title="Code">💻</a> <a href="#ideas-deevus" title="Ideas, Planning, & Feedback">🤔</a></td><td align="center"><a href="https://gymshark.com"><img src="https://avatars3.githubusercontent.com/u/5494442?v=4" width="100px;" alt="Dan Lake"/><br /><sub><b>Dan Lake</b></sub></a><br /><a href="https://github.com/Mezzle/grumphp-tasks/commits?author=danlake" title="Code">💻</a></td><td align="center"><a href="https://github.com/NBZ4live"><img src="https://avatars3.githubusercontent.com/u/605126?v=4" width="100px;" alt="Sergey"/><br /><sub><b>Sergey</b></sub></a><br /><a href="https://github.com/Mezzle/grumphp-tasks/commits?author=NBZ4Live" title="Code">💻</a></td></tr></table>

<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!
