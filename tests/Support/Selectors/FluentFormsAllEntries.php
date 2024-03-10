<?php
namespace Tests\Support\Selectors;
class FluentFormsAllEntries
{
    const apiLogPage = "wp-admin/admin.php?page=fluent_forms_transfer#apilogs";
    const allEntriesPage = "wp-admin/admin.php?page=fluent_forms_all_entries";
    const viewEntry = "(//span[contains(text(),'View')])[1]";
    const apiCalls = "(//span[normalize-space()='API Calls'])[1]";
    const logSuccessStatus = "//span[contains(@class,'ff_tag log_')]";
    const noLogFound = "//p[normalize-space()='Sorry, No Logs found!']";

}
