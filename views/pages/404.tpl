{*SMARTY*}
{add_layout file='common_layout'}
{block name='main'}
   <h1>Page not found</h1>
   <p>The page you are looking for is not found</p>
   Configuration:
   <ul>
      <li>path: {add::config()->path|escape}</li>
      <li>controller rewritten: {$smarty.get.add_mvc_path|escape}</li>
      <li>current controller: {add::current_controller_basename()|escape}</li>
   </ul>
{/block}