# Contributing Phosphorum

Phosphorum is an open source project and a volunteer effort. Phosphorum welcomes contribution from everyone.

## Contributions

Contributions to Phosphorum should be made in the form of GitHub pull requests.
Each pull request will be reviewed by a core contributor (someone with permission to land patches) and either landed in
the main tree or given feedback for changes that would be required before it can be merged. All contributions should
follow this format, even those from core contributors.

## Questions & Support

_We only accept bug reports, new feature requests and pull requests in GitHub._ For questions regarding the usage of the
Phosphorum or support requests please visit the [official forums][:forums:].

## Bug Report Checklist

* Make sure you are using the latest released version of Phosphorum before submitting a bug report.
  Bugs in versions older than the latest released one will not be addressed by the core team

* If you have found a bug it is important to add relevant reproducibility information to your issue to allow us to
  reproduce the bug and fix it quicker. Add a script, small program or repository providing the necessary code to make
  everyone reproduce the issue reported easily. If a bug cannot be reproduced by the development it would be difficult
  provide corrections and solutions

* Be sure that information such as OS, Phalcon Framework and Phosphorum versions and PHP version are part of the
  bug report

* If you're submitting a Segmentation Fault error, we would require a backtrace, please see
  [Generating a Backtrace][:bt:]

## Pull Request Checklist

* Don't submit your pull requests to the master branch. Branch from the required branch and, if needed, rebase to the
  proper branch before submitting your pull request. If it doesn't merge cleanly with master you may be asked to
  rebase your changes

* Don't put submodule updates, composer.lock, etc in your pull request unless they are to landed commits

* Make sure that the code you write fits with the general style and coding standards of the
  [Accepted PHP Standards][:psr:]

## Getting Support

If you have a question about how to use Phalcon, please see the [support page][:support:].

## Requesting Features

If you have a change or new feature in mind, please fill an [NFR][:nfr:].

Thanks! <br />
Phalcon Team


[:forums:]: https://forum.phalconphp.com/
[:bt:]: https://github.com/phalcon/cphalcon/wiki/Generating-a-backtrace
[:psr:]: http://www.php-fig.org/psr/
[:support:]: https://phalconphp.com/support
[:nfr:]: https://github.com/phalcon/cphalcon/wiki/New-Feature-Request---NFR
