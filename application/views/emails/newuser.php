Hello, <?= $user->username ?>

You have been added as a user to the Bravado OPM system. You can log in using the information below.

<?= base_url(); ?>

Login: <?= $user->login ?>

Password: <?= $password ?>


For instructions on how to use the OPM system, please refer to the following manual(s).

<? if (isset($user->perms['can_view_external_manual'])) { ?>
- <?= base_url();?>resources/files/manuals/Bravado_OPM_2_external_user_manual.pdf
<? } ?>
<? if (isset($user->perms['can_view_client_manual'])) { ?>
- <?= base_url();?>resources/files/manuals/Client_User_Manual_OPM_2.pdf
<? } ?>
<? if (isset($user->perms['can_view_internal_manual'])) { ?>
- <?= base_url();?>resources/files/manuals/INTERNAL_Bravado_OPM_2_User_Manual.pdf
<? } ?>
<? if (isset($user->perms['can_view_administrator_manual'])) { ?>
- <?= base_url();?>resources/files/manuals/OPM_2_Administrator_User_Manual.pdf
<? } ?>
<? if (isset($user->perms['can_view_external_viewing_manual'])) { ?>
- <?= base_url();?>resources/files/manuals/Bravado_External_Viewing_Only_User_Manual.pdf
<? } ?>
<? if (isset($user->perms['can_view_licensee_manual'])) { ?>
- <?= base_url();?>resources/files/manuals/Bravado_Licensee_user_manual.pdf
<? } ?>


If you have any questions, please contact <? $this->config->item('supportEmail') ?>.



Thanks!

Bravado OPM.