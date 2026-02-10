# Twig Templates Structure

## البنية التنظيمية

```
views/
├── layouts/          # القوالب الأساسية
│   └── base.twig    # القالب الأساسي (header + footer + content blocks)
│
├── components/       # المكونات المشتركة
│   ├── header.twig  # الهيدر العام
│   └── footer.twig  # الفوتر العام
│
└── *.twig           # قوالب الصفحات (front-page.twig, etc.)
```

## كيفية الاستخدام

### استخدام Layout مع Header/Footer:
```twig
{% extends "layouts/base.twig" %}

{% block title %}Page Title{% endblock %}

{% block head_styles %}
    <link rel="stylesheet" href="{{ theme_uri }}/assets/page/style.css">
{% endblock %}

{% block content %}
    <!-- Page content here -->
{% endblock %}
```

### استخدام Layout بدون Header/Footer:
```twig
{% block header %}{% endblock %}
{% block footer %}{% endblock %}
```

### استخدام Components مباشرة:
```twig
{% include 'components/header.twig' %}
```

