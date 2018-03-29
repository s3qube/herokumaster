<a href="<?= base_url(); ?>users/search/0/0/0/0">Manage Users</a>
<? if (checkPerms('can_email_users')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>users/sendEmail">Email Users</a><? } ?>
&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>usergroups/showall">Manage Usergroups</a>
<? if (checkPerms('can_assign_properties')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>usergroups/assignProperties">Assign Properties</a><? } ?>
<? if (checkPerms('can_manage_offices')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>offices/showall">Offices</a><? } ?>
<? if (checkPerms('can_manage_categories')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>categories">Categories</a><? } ?>
<? if (checkPerms('can_manage_bodystyles')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>bodystyles">Body Styles</a><? } ?>
<? if (checkPerms('can_manage_accounts')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>accounts/search">Accounts</a><? } ?>
<? if (checkPerms('can_manage_terms_of_service')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>tos/search">TOS</a><? } ?>