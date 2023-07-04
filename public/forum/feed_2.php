<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="en-gb">
<link rel="self" type="application/atom+xml" href="forum/feed.php" />

<title>TheLegendOfRandom.com</title>
<subtitle>Reverse Engineering and Programming</subtitle>
<link href="forum/index.php" />
<updated>2013-05-12T19:20:25+00:00</updated>

<author><name><![CDATA[TheLegendOfRandom.com]]></name></author>
<id>forum/feed.php</id>
<entry>
<author><name><![CDATA[tcm1998]]></name></author>
<updated>2013-05-12T19:20:25+00:00</updated>
<published>2013-05-12T19:20:25+00:00</published>
<id>forum/viewtopic.php?t=772&amp;p=42488#p42488</id>
<link href="forum/viewtopic.php?t=772&amp;p=42488#p42488"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Re: Tutorial 16A Question]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=772&amp;p=42488#p42488"><![CDATA[
Question 1 was never really answered, so I'd like to give it a go. Sorry for the late response. I didn't find this site earlier. And just maybe it'll put some new life in this site. That would be great.<br /><br />the WM_COMMAND message has two parameters, which depends on what screen element it handles. For buttons and other controls these parameters are as follows:<br /><br />1) WParam (ARG.3 or EBP+16) contains the control ID in it's LOW WORD (bits 0-15) and the control notification code in the HIGH WORD (bits 16-31).<br />2) LParam (ARG.4 or EBP+20, but not used in this crackme) contains the handle to the window that generated the message.<br /><br />in case of this crackme:<br /><br />MOV EAX,[ARG.3];    Load EAX with WParam<br />MOV EDX,[ARG,3];    Load it in EDX aswell<br />SHR EDX,10;            Shift EDX 16 bits, putting the control notification code into bit 0-15<br />OR DX,DX;               Set Flags according to DX (bit 0-15)<br />JNZ ......                 If it's nonzero, the notification code that is, jump over the button handling code<br /><br />And the notification in question is BN_CLICKED (Button notification clicked)<br /><br />In other words, ignore all messages that are not BN_CLICKED.<br /><br />Hope this helps.<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=23375">tcm1998</a> — Sun May 12, 2013 7:20 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[tcm1998]]></name></author>
<updated>2013-05-07T17:20:08+00:00</updated>
<published>2013-05-07T17:20:08+00:00</published>
<id>forum/viewtopic.php?t=36930&amp;p=39729#p39729</id>
<link href="forum/viewtopic.php?t=36930&amp;p=39729#p39729"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Avast won't let me download tutorial 22]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=36930&amp;p=39729#p39729"><![CDATA[
it complains about Win32:Malware-gen in crackme.exe. Is this a known problem? I have turned avast off and downloaded it anyway, but it's still nagging me a bit. I will be very careful with this program (probably only run in vmware).<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=23375">tcm1998</a> — Tue May 07, 2013 5:20 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[tcm1998]]></name></author>
<updated>2013-05-07T17:13:41+00:00</updated>
<published>2013-05-07T17:13:41+00:00</published>
<id>forum/viewtopic.php?t=36929&amp;p=39728#p39728</id>
<link href="forum/viewtopic.php?t=36929&amp;p=39728#p39728"/>
<title type="html"><![CDATA[Welcome • Hi]]></title>

<category term="Welcome" scheme="forum/viewforum.php?f=11" label="Welcome"/>
<content type="html" xml:base="forum/viewtopic.php?t=36929&amp;p=39728#p39728"><![CDATA[
Ik stumbled upon this site while searching for anti-debugging tricks. I'm dealing with a program that doesn't let me run it under any debugger (ida, olly and visual studio) and olly is even killed as soon as the program starts!! I have read the first 7 tutorials and enjoyed them very much. I do have some asm knowledge, but it has been sunk into the deep corners of my brain.<br /><br />I have done a little reverse engineering, but that was in the DOS time era and, before that, on the MSX computer. Assembly was a LOT easier on the Z80. Ages ago I was planning to follow the +ORC tutorial, but I just never came around to it.<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=23375">tcm1998</a> — Tue May 07, 2013 5:13 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[ItsPauloRoberto]]></name></author>
<updated>2013-05-03T19:43:43+00:00</updated>
<published>2013-05-03T19:43:43+00:00</published>
<id>forum/viewtopic.php?t=36155&amp;p=38915#p38915</id>
<link href="forum/viewtopic.php?t=36155&amp;p=38915#p38915"/>
<title type="html"><![CDATA[Miscellaneous • EXETools Invite!!]]></title>

<category term="Miscellaneous" scheme="forum/viewforum.php?f=10" label="Miscellaneous"/>
<content type="html" xml:base="forum/viewtopic.php?t=36155&amp;p=38915#p38915"><![CDATA[
Hello guys, im new here, im sorry for the incovenience but i just want to download a small file in EXETools forum, but i cant register cause the register is disabled, need activation code(a invite) and i really dont have one.<br /><br />Here is the attachment id for the file, can someone download it and send to me or invite me on the EXETools forum ?<br /><br />I really really need that file....<br /><br />Here is the attachment for the file:<br /><br />attachment.php?<span style="font-weight: bold"><span style="color: #000000">s</span>=<span style="color: #FF0000">246fe4cc84a794d92a9c0a57ae2d1537</span><span style="color: #0000FF">&amp;</span><span style="color: #000000">attachmentid</span>=<span style="color: #FF0000">6819</span><span style="color: #0000FF">&amp;</span><span style="color: #000000">d</span>=<span style="color: #FF0000">1366815556</span></span><br /><br />If someone can help me will be wonderful...<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=22651">ItsPauloRoberto</a> — Fri May 03, 2013 7:43 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[manho]]></name></author>
<updated>2013-04-26T16:22:17+00:00</updated>
<published>2013-04-26T16:22:17+00:00</published>
<id>forum/viewtopic.php?t=34242&amp;p=36932#p36932</id>
<link href="forum/viewtopic.php?t=34242&amp;p=36932#p36932"/>
<title type="html"><![CDATA[OllyDBG • RODBG Simple ASCIITable for olly 2.xx]]></title>

<category term="OllyDBG" scheme="forum/viewforum.php?f=14" label="OllyDBG"/>
<content type="html" xml:base="forum/viewtopic.php?t=34242&amp;p=36932#p36932"><![CDATA[
is there any Simple ASCIITable 2.x plugin like in 1.x ?<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=17797">manho</a> — Fri Apr 26, 2013 4:22 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[manho]]></name></author>
<updated>2013-04-26T16:12:54+00:00</updated>
<published>2013-04-26T16:12:54+00:00</published>
<id>forum/viewtopic.php?t=1071&amp;p=36929#p36929</id>
<link href="forum/viewtopic.php?t=1071&amp;p=36929#p36929"/>
<title type="html"><![CDATA[Challenges • Re: Seventh Challenge:- Crackme #4: Use a code cave]]></title>

<category term="Challenges" scheme="forum/viewforum.php?f=20" label="Challenges"/>
<content type="html" xml:base="forum/viewtopic.php?t=1071&amp;p=36929#p36929"><![CDATA[
never late to learn  <img src="forum/images/smilies/icon_mrgreen.gif" alt=":mrgreen:" title="Mr. Green" /><br />Cracmke4_patch.zip<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=17797">manho</a> — Fri Apr 26, 2013 4:12 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[mikosims]]></name></author>
<updated>2013-04-17T23:30:53+00:00</updated>
<published>2013-04-17T23:30:53+00:00</published>
<id>forum/viewtopic.php?t=29534&amp;p=32264#p32264</id>
<link href="forum/viewtopic.php?t=29534&amp;p=32264#p32264"/>
<title type="html"><![CDATA[Questions and Answers • Re: Dongle Key- or Hasp key]]></title>

<category term="Questions and Answers" scheme="forum/viewforum.php?f=16" label="Questions and Answers"/>
<content type="html" xml:base="forum/viewtopic.php?t=29534&amp;p=32264#p32264"><![CDATA[
also this same program uses a &quot;key utility&quot; where i need a &quot;site code&quot; given to me and this is how i would beable to use the program without the dongle - aka a software key! <br /><br />There is a recent version of this software that is cracked where i copy a .dll file into bin folder. is there a tutorial on how to edit a .dll file or such, any help appreciated<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=7687">mikosims</a> — Wed Apr 17, 2013 11:30 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[mikosims]]></name></author>
<updated>2013-04-17T18:30:36+00:00</updated>
<published>2013-04-17T18:30:36+00:00</published>
<id>forum/viewtopic.php?t=29534&amp;p=32205#p32205</id>
<link href="forum/viewtopic.php?t=29534&amp;p=32205#p32205"/>
<title type="html"><![CDATA[Questions and Answers • Dongle Key- or Hasp key]]></title>

<category term="Questions and Answers" scheme="forum/viewforum.php?f=16" label="Questions and Answers"/>
<content type="html" xml:base="forum/viewtopic.php?t=29534&amp;p=32205#p32205"><![CDATA[
Im trying to crack an ollllddd usb dongle key thats blacklisted, tring to get it to work on a new program that needs it, is this possible? or am i wating my time?<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=7687">mikosims</a> — Wed Apr 17, 2013 6:30 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[nwokiller]]></name></author>
<updated>2013-04-16T12:06:17+00:00</updated>
<published>2013-04-16T12:06:17+00:00</published>
<id>forum/viewtopic.php?t=676&amp;p=32115#p32115</id>
<link href="forum/viewtopic.php?t=676&amp;p=32115#p32115"/>
<title type="html"><![CDATA[Crackmes/Keygenmes • Re: TLoR KeygenMe #1]]></title>

<category term="Crackmes/Keygenmes" scheme="forum/viewforum.php?f=21" label="Crackmes/Keygenmes"/>
<content type="html" xml:base="forum/viewtopic.php?t=676&amp;p=32115#p32115"><![CDATA[
Here it is bro, see attachment<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=84">nwokiller</a> — Tue Apr 16, 2013 12:06 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[manho]]></name></author>
<updated>2013-04-16T11:41:47+00:00</updated>
<published>2013-04-16T11:41:47+00:00</published>
<id>forum/viewtopic.php?t=676&amp;p=32113#p32113</id>
<link href="forum/viewtopic.php?t=676&amp;p=32113#p32113"/>
<title type="html"><![CDATA[Crackmes/Keygenmes • Re: TLoR KeygenMe #1]]></title>

<category term="Crackmes/Keygenmes" scheme="forum/viewforum.php?f=21" label="Crackmes/Keygenmes"/>
<content type="html" xml:base="forum/viewtopic.php?t=676&amp;p=32113#p32113"><![CDATA[
<blockquote><div><cite>Saduff wrote:</cite><br />Download:<br /><!-- m --><a class="postlink" href="http://www.mediafire.com/?sd112dzj7932u5m">http://www.mediafire.com/?sd112dzj7932u5m</a><!-- m --><br /></div></blockquote><br />dead link, would you please reupload, please.<br />wanna try it<br /> <img src="forum/images/smilies/icon_mrgreen.gif" alt=":mrgreen:" title="Mr. Green" /><p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=17797">manho</a> — Tue Apr 16, 2013 11:41 am</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[nwokiller]]></name></author>
<updated>2013-04-09T15:40:56+00:00</updated>
<published>2013-04-09T15:40:56+00:00</published>
<id>forum/viewtopic.php?t=28429&amp;p=31080#p31080</id>
<link href="forum/viewtopic.php?t=28429&amp;p=31080#p31080"/>
<title type="html"><![CDATA[Welcome • Re: hello]]></title>

<category term="Welcome" scheme="forum/viewforum.php?f=11" label="Welcome"/>
<content type="html" xml:base="forum/viewtopic.php?t=28429&amp;p=31080#p31080"><![CDATA[
Welcome, we are in a kind of hibernation right now but hope to be active again soon.<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=84">nwokiller</a> — Tue Apr 09, 2013 3:40 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[1873]]></name></author>
<updated>2013-04-09T14:23:43+00:00</updated>
<published>2013-04-09T14:23:43+00:00</published>
<id>forum/viewtopic.php?t=28429&amp;p=31073#p31073</id>
<link href="forum/viewtopic.php?t=28429&amp;p=31073#p31073"/>
<title type="html"><![CDATA[Welcome • hello]]></title>

<category term="Welcome" scheme="forum/viewforum.php?f=11" label="Welcome"/>
<content type="html" xml:base="forum/viewtopic.php?t=28429&amp;p=31073#p31073"><![CDATA[
hi all just stumbled across this site. I love the challenges of programming and have done some reverse engineering in the past and loved it.  <img src="forum/images/smilies/icon_e_geek.gif" alt=":geek:" title="Geek" />  I'm glad I found this site and hope to use it as a refresher to the world of reverse engineering.<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=19372">1873</a> — Tue Apr 09, 2013 2:23 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[hamu786]]></name></author>
<updated>2013-04-07T05:13:07+00:00</updated>
<published>2013-04-07T05:13:07+00:00</published>
<id>forum/viewtopic.php?t=27562&amp;p=30190#p30190</id>
<link href="forum/viewtopic.php?t=27562&amp;p=30190#p30190"/>
<title type="html"><![CDATA[Questions and Answers • A quick Question]]></title>

<category term="Questions and Answers" scheme="forum/viewforum.php?f=16" label="Questions and Answers"/>
<content type="html" xml:base="forum/viewtopic.php?t=27562&amp;p=30190#p30190"><![CDATA[
I apologize if this question has been posted earlier just wanted to ask that where is random?..it's been a while since the guru posted kinda have been thinking where is he?...hope he's doing great...anyone knows where he is do post up..!..cheers!<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=18862">hamu786</a> — Sun Apr 07, 2013 5:13 am</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[them123]]></name></author>
<updated>2013-04-03T23:21:19+00:00</updated>
<published>2013-04-03T23:21:19+00:00</published>
<id>forum/viewtopic.php?t=25872&amp;p=28425#p28425</id>
<link href="forum/viewtopic.php?t=25872&amp;p=28425#p28425"/>
<title type="html"><![CDATA[Questions and Answers • Good keygen tuts]]></title>

<category term="Questions and Answers" scheme="forum/viewforum.php?f=16" label="Questions and Answers"/>
<content type="html" xml:base="forum/viewtopic.php?t=25872&amp;p=28425#p28425"><![CDATA[
I'm not sure if this site is active at all anymore but I'm looking for some good keygenning tutorials - preferably of the quality of the tuts on this site. I'd like to walk through any series of good tutorials specifically relating to keygenning, sp of anyone has any good recommendations, I'd appreciate it!<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=82">them123</a> — Wed Apr 03, 2013 11:21 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[roocoon]]></name></author>
<updated>2013-04-03T17:19:58+00:00</updated>
<published>2013-04-03T17:19:58+00:00</published>
<id>forum/viewtopic.php?t=25738&amp;p=28287#p28287</id>
<link href="forum/viewtopic.php?t=25738&amp;p=28287#p28287"/>
<title type="html"><![CDATA[Questions and Answers • Packer identification]]></title>

<category term="Questions and Answers" scheme="forum/viewforum.php?f=16" label="Questions and Answers"/>
<content type="html" xml:base="forum/viewtopic.php?t=25738&amp;p=28287#p28287"><![CDATA[
Hello.<br />Not only I'm useless with unpacking, now I can't even identify what this target uses. I doubt I'll be able to unpack it but I'd like to know what it uses (assuming it's commercial).<br /><br />Would anybody care to identify the packer used?<br />I've put the file here: <a href="http://www10.zippyshare.com/v/60179359/file.html" class="postlink">http://www10.zippyshare.com/v/60179359/file.html</a><br /><br />Thanks in advance.<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=702">roocoon</a> — Wed Apr 03, 2013 5:19 pm</p><hr />
]]></content>
</entry>
</feed>