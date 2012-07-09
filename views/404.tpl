{*SMARTY*}
{include file="common_header.tpl"}
   <h1>Page not found</h1>
   <p>The page you are looking for is not found</p>
   Configuration:
   <ul>
      <li>path: {$C->path}</li>
      <li>controller rewritten: {$_GET.add_mvc_path}</li>
      <li>current controller: {add::current_controller_basename()}</li>
   </ul>
{include file="common_footer.tpl"}