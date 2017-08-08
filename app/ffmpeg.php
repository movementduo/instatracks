<?php

function square_top($object) {

	$c = "[1]scale=900x900,drawbox=color=white:t=15,format=yuva422p, rotate='0':ow=rotw(1080):oh=1350:c=black@0,zoompan=z='min(zoom+0.15,1.2)':d=15:x='iw/2-(iw/zoom/2)':y='ih/2-(ih/zoom/2)':s=900x900,fade=in:st=0.2:d=0.2:alpha=1,fade=t=out:st=4.5:d=0.2:alpha=1[i0]; \
	[i0]rotate='0.1*sin(15*PI/60*t)':ow=rotw(1080):oh=1350:c=black@0[i1]; \
	[2]scale=900:-1,fade=in:st=0.2:d=0.2:alpha=1,fade=t=out:st=4.5:d=0.2:alpha=1[i2]; \
	[0][i1]overlay=(main_w-overlay_w)/2:120:shortest=1,format=yuva422p[o1]; \
	[o1][i2]overlay=(main_w-overlay_w)/2:y=120:shortest=1,drawtext=fontfile=".FFMPEG_ASSETS."font.otf:text='".$object->video->text_top_line."':fontsize=70:fontcolor=000a8b:alpha='if(lt(t,0.2),0,if(lt(t,0.4),(t-0.2)/0.2,if(lt(t,4.4),1,if(lt(t,4.5),(0.1-(t-4.4))/0.1,0))))':x=(w-text_w)/2:y=180,drawtext=fontfile=".FFMPEG_ASSETS."font.otf:text='".$object->video->text_bottom_line."':fontsize=70:fontcolor=000a8b:alpha='if(lt(t,0.2),0,if(lt(t,0.4),(t-0.2)/0.2,if(lt(t,4.4),1,if(lt(t,4.5),(0.1-(t-4.4))/0.1,0))))':x=(w-text_w)/2:y=260";

	$initial = 'ffmpeg -i '.$object->background.' -loop 1 -i '.$object->image.' -loop 1 -i '.$object->textbox.' -filter_complex ';

	$end = ' -t 4.6 -b:v 10M /tmp/'.$object->video->id.'.mp4';

	$full_command = $initial.'"'.$c.'"'.$end;

	// exec($full_command);
	return $full_command;

}

function portrait_top($object) {

	$c = "[1]scale=-1:1100,drawbox=color=white:t=15,format=yuva420p,fade=in:st=0.4:d=0.2:alpha=1,fade=t=out:st=4.1:d=0.2:alpha=1,rotate='0.1*sin(20*PI/50*t)':ow=rotw(iw):oh=1350:c=black@0[i1]; \
	[2]scale=1000:-1,fade=in:st=0.4:d=0.2:alpha=1,fade=t=out:st=4.1:d=0.2:alpha=1[i2]; \
	[0][i1]overlay=(main_w-overlay_w)/2:250:shortest=1[o1]; \
	[o1][i2]overlay=(main_w-overlay_w)/2:y=80:shortest=1,drawtext=fontfile=".FFMPEG_ASSETS."font.otf:text='".$object->video->text_top_line."':fontsize=70:fontcolor=yellow:alpha='if(lt(t,0.4),0,if(lt(t,0.6),(t-0.4)/0.2,if(lt(t,4),1,if(lt(t,4.2),(0.2-(t-4))/0.2,0))))':x=(w-text_w)/2:y=180,drawtext=fontfile=".FFMPEG_ASSETS."font.otf:text='".$object->video->text_bottom_line."':fontsize=70:fontcolor=yellow:alpha='if(lt(t,0.4),0,if(lt(t,0.6),(t-0.4)/0.2,if(lt(t,4),1,if(lt(t,4.2),(0.2-(t-4))/0.2,0))))':x=(w-text_w)/2:y=260";

	$initial = 'ffmpeg -i '.$object->background.' -loop 1 -i '.$object->image.' -loop 1 -i '.$object->textbox.' -filter_complex ';

	$end = ' -t 4.6 -b:v 10M /tmp/'.$object->video->id.'.mp4';

	$full_command = $initial.'"'.$c.'"'.$end;

	// exec($full_command);
	return $full_command;

}

function landscape_center($object) {

$c = "[1]scale=-1:650,drawbox=color=white:t=15,format=yuva420p,fade=in:st=0.4:d=0.2:alpha=1,fade=t=out:st=4.1:d=0.2:alpha=1,rotate='0.1*sin(20*PI/50*t)':ow=rotw(iw):oh=roth(ih):c=black@0[i1]; \
	[2]scale=-1:650,drawbox=color=white:t=15,format=yuva420p,fade=in:st=0.4:d=0.2:alpha=1,fade=t=out:st=4.1:d=0.2:alpha=1,rotate='-0.1*sin(20*PI/50*t)':ow=rotw(iw):oh=roth(ih):c=black@0[i2]; \
	[3]scale=1000:-1,fade=in:st=0.4:d=0.2:alpha=1,fade=t=out:st=4.1:d=0.2:alpha=1[i3]; \
	[0][i1]overlay=x='if(lte(-w+(t-0.3)*2500,-80),-w+(t-0.3)*2500,-80)':y=-80:shortest=1[o1]; \
	[o1][i2]overlay=x=-'if(lte(-w+(t-0.3)*2500,-120),-w+(t-0.3)*2500,-120)':y=610:shortest=1[o2]; \
	[o2][i3]overlay=(main_w-overlay_w)/2:y=(main_h-overlay_h)/2:shortest=1,drawtext=fontfile=".FFMPEG_ASSETS."font.otf:text='".$object->video->text_top_line."':fontsize=70:fontcolor=yellow:alpha='if(lt(t,0.2),0,if(lt(t,0.4),(t-0.2)/0.2,if(lt(t,4),1,if(lt(t,4.2),(0.2-(t-4))/0.2,0))))':x=(w-text_w)/2:y=570, drawtext=fontfile=".FFMPEG_ASSETS."font.otf:text='".$object->video->text_bottom_line."':fontsize=70:fontcolor=yellow:alpha='if(lt(t,0.2),0,if(lt(t,0.4),(t-0.2)/0.2,if(lt(t,4),1,if(lt(t,4.2),(0.2-(t-4))/0.2,0))))':x=(w-text_w)/2:y=650";

	$initial = 'ffmpeg -i '.$object->background.' -loop 1 -i '.$object->image.' -loop 1 -i '.$object->image.' -loop 1 -i '.$object->textbox.' -filter_complex ';

	$end = ' -t 4.6 -b:v 10M /tmp/'.$object->video->id.'.mp4';

	$full_command = $initial.'"'.$c.'"'.$end;

	// exec($full_command);
	return $full_command;

}

function join_videos($imgs,$instanceID) {
	$intro = FFMPEG_ASSETS."Intro.mp4";
	$end = FFMPEG_ASSETS."End.mp4";

	$vids = [];
	foreach($imgs as $i) {
		$vids[] = ' -i /tmp/'.$i->id.'.mp4';
	}


	$name = "/tmp/addmusic-{$instanceID}.mp4";

	$cmd = "ffmpeg -i ".$intro.implode($vids)." -i ".$end." -filter_complex concat=n=".(count($vids) + 2).":v=1:a=1 -c:v libx264 ".$name;
	
	shell_exec($cmd);
}

function add_music($audioUrl,$instanceID) {

	$vid = "/tmp/addmusic-{$instanceID}.mp4";
	$music = $audioUrl;
	$fin = "/tmp/finished-{$instanceID}.mp4";

	$command = "ffmpeg -i ".$vid." -i ".$music." \
	-c:v copy -c:a aac -strict experimental \
	-map 0:v:0 -map 1:a:0 ".$fin;

	shell_exec($command);
}
