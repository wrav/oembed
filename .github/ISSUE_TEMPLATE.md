# Bug Report

Thanks for reporting an issue. Please fill this out so we can troubleshoot quickly.

## Summary
<!-- What is going wrong? -->

## Steps to Reproduce
1. 
2. 
3. 

## Expected Behavior
<!-- What did you expect to happen? -->

## Actual Behavior
<!-- What happened instead? Include error text if available. -->

## Environment
- **CraftCMS version:**
- **oEmbed plugin version:**
- **PHP version:**
- **Database (optional):** <!-- e.g., MySQL 8.0 / MariaDB 10.6 / PostgreSQL 15 -->
- **Web server (optional):** <!-- e.g., Nginx / Apache -->
- **OS (optional):**

## URL/Provider Details
- **Provider:** <!-- e.g., YouTube, Vimeo, Instagram -->
- **Example URL(s):**

## Template / Code Used
<!-- Paste the template code or field usage, e.g. `entry.oembedField.render()` -->

## Logs / Errors
<!-- Paste relevant Craft logs, PHP errors, stack traces, or screenshots -->

## Error Messages / Screenshots
<!-- Paste exact error messages and attach screenshots from the CP or frontend where helpful -->

## Additional Context
<!-- Anything else that might help us reproduce or debug -->

### Admin CP Issues (if applicable)
- Field preview not showing?
- Save/validation problems?
- Settings interface issues?

### Frontend / Template Context (if applicable)
- Template method used: `render()` / `embed()` / `media()` / `valid()`
- Cache enabled or disabled?
- GDPR settings active?
- Is this GraphQL-related?

### Provider-Specific Notes (if applicable)
- Does the URL work on the provider's site?
- Using embed URL vs regular/shared URL?
- Any provider API tokens configured (for Instagram/Facebook)?

---

## Pro Tips
- Some providers require **embed URLs** instead of regular watch/share URLs.
- Try disabling cache temporarily to check whether this is cache-related.
- For Instagram, confirm Facebook API token configuration.
- For GDPR mode, check whether privacy settings are blocking the expected embed behavior.
