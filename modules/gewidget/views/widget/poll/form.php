<?php defined('SYSPATH') or die('No direct script access.');?>

<form action="/widget/wpoll/submit/" method="post" onsubmit="onWpollSubmit(this);return!1;">
	<strong><?=$caption?></strong>
	<?=$answer?>
	<input type="hidden" name="question_id" value="<?=$id?>" />
	<input type="image" alt="Голосовать" title="Голосовать" src="/rs/images/vote.gif" class="inp-vote" value="" />
</form>
    
<a href="#" onclick="onWpollResult(<?=$id?>);return!1;"> Посмотреть результаты </a>