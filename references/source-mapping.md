# Source Mapping Notes

## User-provided target page

- `https://hostswp.com/wp-admin/admin.php?page=hostwp-clarity-recordings`

## What was verified from this environment

1. Requesting the target URL without auth redirects to:
   - `wp-login.php?redirect_to=...hostwp-clarity-recordings`
2. No matching local PHP source containing these strings was found in the current filesystem:
   - `hostwp-clarity-recordings`
   - `clarity`
   - `Microsoft Clarity`

## Interpretation

This means the current machine could confirm the target admin page exists behind login, but could not verify a corresponding local plugin source tree to extract function names from.

So this skill/repo is built as a **portable Microsoft Clarity UX dashboard pattern** inspired by the target page name and the user's requested behavior:

- enter a key / token / project config
- analyze UX behavior data for that site
- output readable UX suggestions
- present everything in a premium reusable dashboard
- render in general HTML, not only WordPress admin

## Portable dashboard concept

Recommended normalized modules:

- summary cards
- issue cards
- behavior trends
- problem pages
- recordings list
- device/source segments
- UX recommendations
- alerts / data-source status
