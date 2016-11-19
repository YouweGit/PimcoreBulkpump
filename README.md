PIMCORE BULKPUMP EXTENSION
--------------------------

Version: Pimcore 4.x

Developed by: Roelf

Reference / latest developments: Roelf


Usage and examples
------------------

Click on "Product importer" on the left side.
Documentation is available in the "doc" folder of this plugin.


Installation
------------

Plugin can be installed through composer. Add json to your composer.json:

    {
        "config": {
            "document-root-path": "htdocs"
        },
        "require": {
            "youwe/pimcore-bulkpump": "^0.4.0"
        },
    }


Also, add these to your .gitignore:

    /htdocs/plugins/PimcoreBulkpump


Plugin development
------------------

To create a new version, check out the master branch somewhere and go:

    git tag 0.1.0
    git push origin --tags


Documentation / list of functionalities
---------------------------------------

Pick a custom class to import CSV columns to.

o_key column mapping will be used to create the Pimcore key and to update existing objects.

If an object has an import[FieldName] function, this function will be used instead of the set[FieldName] function.

Custom filters can be added to the project here:

    /htdocs/website/models/BulkPumpFilter

Custom filters must have the same format as the native filters here:

    /htdocs/plugins/BulkPump/models/BulkPump/ImportFilter/Native

A completely custom import class can be used, overriding most standard functionalities:

    /htdocs/website/lib/Website/BulkPump/CustomImport.php


Run from commandline (CLI)
--------------------------

You can import CSV files by running from commandline, you can configure the profile from the interface.
You could change the content of the file in the profile by changing in the following path:

    /htdocs/website/var/tmp/bulk-pump

Then you can call the profile with the following command:

    php ./plugins/PimcoreBulkpump/cli/import.php --profileId=<profile id>

You have to change `<profile id>` in the profile id you want to run. You can find the id in the GUI of the plugin.

Security
--------

User must have this permission to use the plugin: 
*   plugin_bulkpump_user

Future permissions:
*   plugin_bulkpump_admin
*   plugin_bulkpump_import
*   etc..



