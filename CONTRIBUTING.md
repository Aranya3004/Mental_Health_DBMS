# Contributing to Mental Health DBMS

First off, thank you for considering contributing to Mental Health DBMS! It's people like you that make this project better for everyone.

## Code of Conduct

By participating in this project, you are expected to uphold our code of conduct:
- Be respectful and inclusive
- Welcome newcomers and help them learn
- Focus on what's best for the community
- Show empathy towards other community members

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the existing issues to avoid duplicates. When you create a bug report, include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples** to demonstrate the steps
- **Describe the behavior you observed** and what you expected
- **Include screenshots** if possible
- **Mention your environment**: OS, PHP version, MySQL version, browser

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, include:

- **Use a clear and descriptive title**
- **Provide a detailed description** of the proposed feature
- **Explain why this enhancement would be useful**
- **List some examples** of how it would be used

### Pull Requests

1. Fork the repo and create your branch from `main`
2. If you've added code that should be tested, add tests
3. Ensure your code follows the existing style
4. Update the documentation if needed
5. Make sure your code passes all tests
6. Issue that pull request!

## Development Process

### Setup Development Environment

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/MentalHealth_DBMS.git

# Create a branch
git checkout -b feature/amazing-feature

# Make your changes
# ...

# Commit your changes
git add .
git commit -m "Add amazing feature"

# Push to your fork
git push origin feature/amazing-feature
```

### Coding Standards

#### PHP
- Use PSR-12 coding standards
- Add meaningful comments
- Use descriptive variable names
- Follow camelCase for variables and functions
- Follow PascalCase for class names

```php
// Good
function getUserSessionData($userId) {
    // Implementation
}

// Bad
function get_data($u) {
    // Implementation
}
```

#### SQL
- Use UPPERCASE for SQL keywords
- Use snake_case for table and column names
- Always use prepared statements

```sql
-- Good
SELECT user_id, email FROM users WHERE is_active = 1;

-- Bad
select userId from Users where active = 1
```

#### HTML/CSS
- Use semantic HTML5 elements
- Keep CSS organized and commented
- Use meaningful class names
- Follow mobile-first approach

### Database Changes

If your contribution involves database changes:

1. Create a migration SQL file in `database/migrations/`
2. Name it: `YYYY_MM_DD_description.sql`
3. Include both UP and DOWN migration
4. Document the changes in the PR

### Testing

Before submitting:
- Test all new features
- Test on different browsers (Chrome, Firefox, Edge)
- Test responsive design
- Verify database operations
- Check for PHP errors

## Areas We Need Help With

### High Priority
- [ ] Security improvements (XSS, CSRF, SQL injection prevention)
- [ ] Mobile responsiveness
- [ ] Performance optimization
- [ ] Accessibility (WCAG compliance)

### Medium Priority
- [ ] UI/UX improvements
- [ ] Additional AI/ML features
- [ ] Email notifications
- [ ] SMS alerts integration
- [ ] Multi-language support

### Low Priority
- [ ] Documentation improvements
- [ ] Code refactoring
- [ ] Test coverage
- [ ] Dark mode

## Style Guidelines

### Git Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters
- Reference issues and pull requests after the first line

```
Add user authentication feature

- Implement login functionality
- Add password hashing
- Create session management

Fixes #123
```

### Documentation

- Use Markdown for documentation
- Keep README.md up to date
- Add inline comments for complex logic
- Update CHANGELOG.md

## Community

- Join discussions in GitHub Discussions
- Ask questions in Issues (label: question)
- Share your ideas in Feature Requests
- Help others by reviewing PRs

## Recognition

Contributors will be acknowledged in:
- README.md Contributors section
- Release notes
- Special thanks in documentation

## Questions?

Don't hesitate to ask! You can:
- Open an issue with label "question"
- Contact the maintainers
- Join our community discussions

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for making Mental Health DBMS better! ❤️
