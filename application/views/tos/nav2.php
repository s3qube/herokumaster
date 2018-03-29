<a href="<?= base_url(); ?>users/search/0/0/0/0">Manage Users</a>
&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>usergroups/showall">Manage Usergroups</a>
<? if (checkPerms('can_assign_properties')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>usergroups/assignProperties">Assign Properties</a><? } ?>
<? if (checkPerms('can_manage_categories')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>categories">Manage Categories</a><? } ?>
<? if (checkPerms('can_email_users')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>users/sendEmail">Email Users</a><? } ?>