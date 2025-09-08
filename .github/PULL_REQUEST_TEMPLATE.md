# 🚀 Pull Request

**Thanks for contributing to the oEmbed Craft CMS plugin!** Please fill out the sections below to help us review your changes.

## 📝 What does this PR do?
<!-- Brief description of your changes -->

## 🔗 Related Issue
<!-- Link to the issue this PR addresses -->
Fixes #

## 🎯 Type of Change
<!-- Put an `x` in the box that applies -->
- [ ] 🐛 **Bug fix** (fixes an issue without breaking existing functionality)
- [ ] ✨ **New feature** (adds functionality without breaking changes)  
- [ ] 💥 **Breaking change** (fix/feature that changes existing functionality)
- [ ] 🏗️ **Refactor** (code improvement without functional changes)
- [ ] 📚 **Documentation** (README, comments, or docs)
- [ ] 🧪 **Tests** (adding or updating tests)

## 🧪 Testing

### How did you test this?
<!-- Describe your testing approach -->
- [ ] Added/updated unit tests
- [ ] Tested with multiple embed providers (YouTube, Vimeo, etc.)
- [ ] Tested admin CP field functionality
- [ ] Tested frontend rendering
- [ ] Tested caching behavior
- [ ] Tested GDPR compliance features
- [ ] Manual testing in Craft CMS environment

### Test Environment
- **Craft CMS version**: 
- **PHP version**: 
- **Tested providers**: <!-- e.g., YouTube, Vimeo, Instagram -->

## ✅ Checklist
<!-- Put an `x` in completed boxes -->

### Code Quality
- [ ] My code follows the existing code style
- [ ] I've added comments where code is complex
- [ ] No new warnings or errors introduced

### Functionality  
- [ ] Field works correctly in Craft CP
- [ ] Frontend rendering works as expected
- [ ] Caching behaves properly
- [ ] GDPR settings are respected (if applicable)
- [ ] GraphQL queries work (if applicable)

### Testing & Documentation
- [ ] I've added/updated tests for my changes
- [ ] All existing tests still pass
- [ ] I've updated documentation if needed
- [ ] I've tested edge cases and error scenarios

### Plugin-Specific
- [ ] Handles provider URL variations (embed vs regular URLs)
- [ ] Fallback behavior works for unsupported providers
- [ ] Network/timeout errors are handled gracefully
- [ ] API token requirements documented (if applicable)

## 🔍 Review Focus Areas
<!-- Help reviewers know what to focus on -->
- [ ] **Admin UI**: Changes to the control panel field interface
- [ ] **Template Methods**: Changes to `render()`, `embed()`, `media()`, `valid()` methods
- [ ] **Provider Support**: New or modified provider handling
- [ ] **Caching Logic**: Changes to cache behavior
- [ ] **GDPR Features**: Privacy compliance modifications
- [ ] **Error Handling**: Network/provider failure scenarios

## 📸 Screenshots (if applicable)
<!-- Add screenshots for UI changes -->

## 💭 Additional Notes
<!-- Any other context, concerns, or questions for reviewers -->

---

**🔄 Ready for Review?** Make sure all tests pass and the CI checks are green!