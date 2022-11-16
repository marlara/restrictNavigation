{**
    * plugins/generic/restrictNavigation/templates/settings.tpl
    *
    * Copyright Lara Marziali
    * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
    *
    * Settings form for the restrictNavigation plugin.
    *}
   <script>
       $(function() {ldelim}
           $('#restrictNavigationSettings').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
       {rdelim});
   </script>
   
   {translate key="plugins.generic.restrictNavigation.setting.description"}
   
   <form
       class="pkp_form"
       id="restrictNavigationSettings"
       method="POST"
       action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}"
   >
       <!-- Always add the csrf token to secure your form -->
       {csrf}

   
       <table class="pkpTable">
           <thead>
               <td></td>
               <th>{translate key="plugins.generic.restrictNavigation.setting.generalSettings"}</th>
               <th>{translate key="plugins.generic.restrictNavigation.setting.tools"}</th>
               <th>{translate key="plugins.generic.restrictNavigation.setting.workflow"}</th>
           </thead>
           <tbody>
               <tr>
                   <th scope="row">{translate key="plugins.generic.restrictNavigation.setting.onlyAdmin"}</th>
                   <td><input type="checkbox" name="generalSettings" value="generalSettings" checked=$general_settings></td>
                   <td><input type="checkbox" name="tools" value="tools" checked=$tools></td>
                   <td><input type="checkbox" name="workflow" value="workflow" checked=$workflow></td>
               </tr>
           </tbody>
       </table>
   
       {fbvFormButtons submitText="common.save"}
   </form>