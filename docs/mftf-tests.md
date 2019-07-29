---
layout: full-width
title: MFTF Tests
---

The Magento Functional Testing Framework runs tests on every Module within Magento. These files are stored within each Module folder in the Magento repo.
This page lists all those tests so that developers can have a good sense of what is covered.

{% assign mftf = site.data.mftf | group_by: "module" | sort: "name"  %}

{% for item in mftf %}

### {{ item.name }}
{% for file in item.items %}
#### [{{ file.filename }}]({{file.repo}})

{% for test in file.tests %}
{{test.testname}}
  : {{test.description}}

{% endfor %}
{% endfor %}
{% endfor %}
