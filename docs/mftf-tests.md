<style>
.mftf-dl {
  margin-bottom: 2.5em;
}
dl dt{
  font-weight:400;
}
</style>

# MFTF functional test reference

This page lists all existing tests so that developers can have a good sense of what is covered.

{% include mftf/functional_data.md %}

{% for item in mftf %}

### {{ item.name }} 
{% for file in item.items %}
#### [{{ file.filename }}]({{file.repo}})
{: .mftf-test-link}

{% for test in file.tests %}
{{test.testname}}
  : {{test.description}}
{: .mftf-dl}
{% endfor %}
{% endfor %}
{% endfor %}
