# GitHub Pages Site Configuration

This directory contains the GitHub Pages portfolio site for the SCM Ferretería project.

## Structure

```
docs/
├── index.html           # Main portfolio page
├── assets/
│   └── style.css       # Styles for the portfolio
└── reports/
    ├── index.html      # Test reports showcase page
    └── *.html          # Individual test reports
```

## Local Preview

To preview the site locally, you can use Python's built-in HTTP server:

```bash
cd docs
python3 -m http.server 8000
```

Then open http://localhost:8000 in your browser.

## GitHub Pages Setup

This site is configured to be served from the `/docs` folder on the main branch.

See the main README for deployment instructions.
