
# MFTF Test Reference

The Magento Functional Testing Framework runs tests on every Module within Magento.

These files are stored within each Module folder in the Magento repository.
This page lists all those tests so that developers have a good sense of what is covered.

{% assign mftf = site.data.mftf | group_by: "module"  %}
{% assign sorted = mftf | sort: "name" %}

{% for module in sorted %}

## {{ module.name }}

{% assign tests = module.items | sort: "filename" %}
{% for item in tests %}

#### [{{ item.testname }}]({{ item.link }})

{{ item.description}}

{% endfor %}
{% endfor %}