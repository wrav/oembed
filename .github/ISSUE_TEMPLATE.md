# 🐛 Bug Report / 💡 Feature Request

**Thanks for contributing to the oEmbed Craft CMS plugin!** This template helps us understand issues with the field type, embed functionality, caching, and admin interface.

## 📋 Issue Type
- [ ] 🐛 Bug report
- [ ] 💡 Feature request  
- [ ] 📚 Documentation issue
- [ ] ❓ Question/Support

---

## 🔍 **Bug Report** (Skip if feature request)

### What's the issue?
<!-- Clear description of what's wrong -->

### Where does it happen?
- [ ] **Admin CP Field**: Issue in the Craft control panel field interface
- [ ] **Frontend Render**: Problem with `{{ entry.field.render() }}` output
- [ ] **Caching**: Cached content not updating or cache errors
- [ ] **GDPR Compliance**: Issues with privacy settings (YouTube no-cookie, Vimeo DNT, etc.)
- [ ] **Network/Provider**: Provider-specific embed failures
- [ ] **GraphQL**: Issues with GraphQL field queries

### Steps to reproduce
1. 
2. 
3. 

### Expected vs Actual
**Expected:** 
**Actual:** 

### Your Environment
- **Craft CMS**: <!-- e.g., 4.5.0 -->
- **oEmbed Plugin**: <!-- e.g., 3.1.5 -->
- **PHP**: <!-- e.g., 8.2 -->
- **Provider**: <!-- e.g., YouTube, Vimeo, Instagram, Twitter -->
- **Test URL**: <!-- The URL you're trying to embed -->

---

## 💡 **Feature Request** (Skip if bug report)

### What feature would you like?
<!-- Clear description -->

### What problem does this solve?
<!-- Context about why this is needed -->

### Suggested implementation
<!-- How you think it should work -->

---

## 🔧 Additional Context

### Admin CP Issues (if applicable)
- Field preview not showing?
- Save/validation problems?
- Settings interface issues?

### Frontend Issues (if applicable)
- Template method used: `render()` / `embed()` / `media()` / `valid()`
- Cache enabled/disabled?
- GDPR settings active?

### Provider-Specific Issues
- Does the URL work on the provider's site?
- Using embed URL vs regular URL?
- API tokens configured (for Instagram/Facebook)?

### Error Messages/Screenshots
<!-- Paste any error messages or attach screenshots -->

---

**💡 Pro Tips:**
- Many providers need **embed URLs** not regular URLs (check provider's share → embed option)
- Check your **cache settings** - try disabling cache temporarily to test
- For Instagram: requires Facebook API token in plugin settings
- For GDPR: check if privacy settings are affecting embeds