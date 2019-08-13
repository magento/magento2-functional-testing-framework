<style>
.mftf-dl {
  margin-bottom: 2.5em;
}
dl dt{
  font-weight:500;
}

</style>

# MFTF action group reference

Action groups are important building blocks for quickly creating tests for the Magento Functional Testing Framework (MFTF).
This page lists all current action groups so that developers can see what is available to them.

{% include mftf/actiongroup_data.md %}

{% for item in actiongroups %}

### {{ item.name }} 
{% for file in item.items %}
#### [{{ file.filename }}]({{file.repo}})

{% for test in file.actiongroups %}
{{test.name}}
  : {% if test.description == '' %}No description.{% else %}{{test.description}}{% endif %}
{: .mftf-dl}
{% endfor %}
{% endfor %}
{% endfor %}
