# Module Settings Plugin #

This plugin provides REDCap administrators with a way to import and export a chosen external module s settings as a .csv file.

## Instructions ##

After placing the module-settings plugin folder in your redcap/plugins directory, you can visit the plugin to export/import module settings.

1. Select an external module.
2. Select a scope -- should the plugin export project values only, system values only, or both?
3. Depending on the scope chosen, you may need to select a specific project as well.
4. Either export by clicking "Download .CSV" button in the "Export" panel, or upload a settings file (formatted like an export .csv) and click "Import .CSV".

Sample export and interface : ![Plugin interface with export button shown](/docs/export_pic.PNG)

### Results ###

Upon importing a settings file, the plugin will generate a list describing which settings were successfully changed or if you do not have the permissions necessary to make those settings change.

Example results panel : ![Plugin example of results panel](/docs/results_example.PNG)