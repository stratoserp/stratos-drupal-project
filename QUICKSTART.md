# Quickstart for Stratos ERP

A Basic .env file has been included to set the SHEPHERD_INSTALL_PROFILE
environment variable, but the quickstart below also has it explicitlty,
just in case you are not using a autoenv/direnv.

```
composer create-project stratoserp/stratos-drupal-project:8.x-dev test --stability dev --no-interaction
cd test
./dsh
SHEPHERD_INSTALL_PROFILE=stratoserp_base robo build
```
