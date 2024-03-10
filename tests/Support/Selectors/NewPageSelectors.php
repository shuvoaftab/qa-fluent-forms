<?php

namespace Tests\Support\Selectors;

class NewPageSelectors
{
    //New page creation with form short code
    const previousPageAvailable = "//input[@id='post-query-submit']";
    const selectAllCheckMark = "(//input[@id='cb-select-all-1'])[1]";
    const selectMoveToTrash = "(//select[@id='bulk-action-selector-top'])[1]";
    const applyBtn = "(//input[@id='doaction'])[1]";
    const formShortCode = "//code[contains(@title,'Click to copy')]";
    const addNewPage = ".page-title-action";
    const jsForTitle = 'wp.data.dispatch("core/editor").editPost({title: "%s"})';
    const jsForContent = "wp.data.dispatch('core/block-editor').insertBlock(wp.blocks.createBlock('core/paragraph',{content:'%s'}))";
    const publishBtn = "(//button[normalize-space()='Publish'])[1]";
    const confirmPublish = "(//button[@class='components-button editor-post-publish-button editor-post-publish-button__button is-primary'])[1]";
    const viewPage = "a[class='components-button is-primary']";
}