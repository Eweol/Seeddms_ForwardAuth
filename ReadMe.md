# SeedDms ForwardAuth Extension

This is a SeedDms extension that provides support for reverse proxy forward auth login via Traefik for example. The username of the user to login must be present in SeedDme and must be forwarded by the reverse proxy via HTTP header.

## Install

Copy the ``forwart_auth`` folder in ``ext`` folder of SeedDms. Open extesions settings and refresh extensions via button. Activate the extension with help of checkbox within extension's config and set the header name, which includes the username.

## Derivat 

This code is inspired and based on [SeedSAML-Extension](https://www.aboehler.at/hg/seedsaml/) from Andreas Böhler