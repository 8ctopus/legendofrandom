<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="en-gb">
<link rel="self" type="application/atom+xml" href="forum/feed.php?f=2" />

<title>TheLegendOfRandom.com</title>
<subtitle>Reverse Engineering and Programming</subtitle>
<link href="forum/index.php" />
<updated>2013-05-12T19:20:25+00:00</updated>

<author><name><![CDATA[TheLegendOfRandom.com]]></name></author>
<id>forum/feed.php?f=2</id>
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
<author><name><![CDATA[nwokiller]]></name></author>
<updated>2013-02-04T00:30:03+00:00</updated>
<published>2013-02-04T00:30:03+00:00</published>
<id>forum/viewtopic.php?t=10600&amp;p=12643#p12643</id>
<link href="forum/viewtopic.php?t=10600&amp;p=12643#p12643"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Re: Need help Tutorial 13]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=10600&amp;p=12643#p12643"><![CDATA[
I checked this with a clean Olly, no problems-no plugins needed. The only anti-debug looked like breakpoint clearing at each restart. Easy, just note down bp's and set on each restart  <img src="forum/images/smilies/icon_mrgreen.gif" alt=":mrgreen:" title="Mr. Green" /> For basic stuff the only plugins you would need are OllyAdvanced and Hidedebugger, these are sufficient. I keep a few Olly versions with different plugins and mods, so if one fails on a target....I try the others, that way I don't have to play with settings all the time(lazy <img src="forum/images/smilies/icon_lol.gif" alt=":lol:" title="Laughing" /> )    <img src="forum/images/smilies/icon_mrgreen.gif" alt=":mrgreen:" title="Mr. Green" /><p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=84">nwokiller</a> — Mon Feb 04, 2013 12:30 am</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[Urban Legend]]></name></author>
<updated>2013-02-03T23:05:41+00:00</updated>
<published>2013-02-03T23:05:41+00:00</published>
<id>forum/viewtopic.php?t=10600&amp;p=12624#p12624</id>
<link href="forum/viewtopic.php?t=10600&amp;p=12624#p12624"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Re: Need help Tutorial 13]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=10600&amp;p=12624#p12624"><![CDATA[
nwokiller, thanks for quick reply, let me ask you this, do you know if r4ndom used any particular plugins on that tut? from the beginning of the tuts we had the option to use either his olly or the standard, seems like he would've noted that if we use the standard that we need to use certain plugins, i have to say that this really put a damper in the series of tuts for me because it was fluent until this point,  <img src="forum/images/smilies/icon_cry.gif" alt=":cry:" title="Crying or Very Sad" />  , aside from this so far these tuts are unmatched, i love the level of detail but tut 13 target seemed like a jump due to the fact that it deploys antidebugger techniques, keep me posted because i can not continue on with out tut 13,  i refuse to skip   Thanks alot<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=9249">Urban Legend</a> — Sun Feb 03, 2013 11:05 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[nwokiller]]></name></author>
<updated>2013-02-03T22:00:10+00:00</updated>
<published>2013-02-03T22:00:10+00:00</published>
<id>forum/viewtopic.php?t=10600&amp;p=12613#p12613</id>
<link href="forum/viewtopic.php?t=10600&amp;p=12613#p12613"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Re: Need help Tutorial 13]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=10600&amp;p=12613#p12613"><![CDATA[
In regard to your programs opening at nt.dll, go to olly's options tab=&gt; debugging =&gt;events and check &quot;make first pause at&quot; entry point of main module. You probably have this set on system breakpoint which would explain your situation. In regard to the tut, I'll have a look for you.<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=84">nwokiller</a> — Sun Feb 03, 2013 10:00 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[Urban Legend]]></name></author>
<updated>2013-02-03T21:40:10+00:00</updated>
<published>2013-02-03T21:40:10+00:00</published>
<id>forum/viewtopic.php?t=10600&amp;p=12607#p12607</id>
<link href="forum/viewtopic.php?t=10600&amp;p=12607#p12607"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Need help Tutorial 13]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=10600&amp;p=12607#p12607"><![CDATA[
First and foremost i have to make it clear that i attended class faithfully from tutorial 1 lol, but im afraid i mustve fell asleep in one of these tuts because im at tut 13,  attempting to run the target app to first break and my target is not performing like the instructions in the tut, i beleive im getting diverted by some sort of debugger trap, but im looking back in these tuts and i dont see where we we learned any anti debugger methods, Also while im here i may as well mention that i dont know why every app i used in these tuts started on ntdll. in debugger, did i miss something somewhere in these tuts ?<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=9249">Urban Legend</a> — Sun Feb 03, 2013 9:40 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[welhim]]></name></author>
<updated>2013-02-02T10:56:34+00:00</updated>
<published>2013-02-02T10:56:34+00:00</published>
<id>forum/viewtopic.php?t=9745&amp;p=12009#p12009</id>
<link href="forum/viewtopic.php?t=9745&amp;p=12009#p12009"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Re: Tutorial #3 please help]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=9745&amp;p=12009#p12009"><![CDATA[
it's a nooby question but thanks <img src="forum/images/smilies/icon_e_smile.gif" alt=":)" title="Smile" /> i can proceed to tutorial #4 now ... <img src="forum/images/smilies/icon_mrgreen.gif" alt=":mrgreen:" title="Mr. Green" /><p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=8983">welhim</a> — Sat Feb 02, 2013 10:56 am</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[bartbilf]]></name></author>
<updated>2013-02-02T01:07:58+00:00</updated>
<published>2013-02-02T01:07:58+00:00</published>
<id>forum/viewtopic.php?t=9745&amp;p=11899#p11899</id>
<link href="forum/viewtopic.php?t=9745&amp;p=11899#p11899"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Re: Tutorial #3 please help]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=9745&amp;p=11899#p11899"><![CDATA[
Right click under the 'Hex dump' it probably says 'Disassemble', make it 'Hex -&gt; Hex/ASCII(16 bytes)<br /><br />Then you have the same view as the tutorial.<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=7382">bartbilf</a> — Sat Feb 02, 2013 1:07 am</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[welhim]]></name></author>
<updated>2013-02-01T17:37:41+00:00</updated>
<published>2013-02-01T17:37:41+00:00</published>
<id>forum/viewtopic.php?t=9745&amp;p=11733#p11733</id>
<link href="forum/viewtopic.php?t=9745&amp;p=11733#p11733"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Tutorial #3 please help]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=9745&amp;p=11733#p11733"><![CDATA[
sir, something wrong with my hex dump it didn’t show same as yours… instead the number will file as row it file as column… <img src="forum/images/smilies/icon_mad.gif" alt=":x" title="Mad" /> <br /><br /><br />my print screen...<br /><img src="http://i877.photobucket.com/albums/ab331/welhim2010/Error/submitintutorial.jpg" alt="Image" /><br /><br /><br /><br /><br />Tutorial #3<br /><!-- m --><a class="postlink" href="http://thelegendofrandom.com/blog/archives/115">http://thelegendofrandom.com/blog/archives/115</a><!-- m --><br /><br /><br />is it because im using 64bit? windows 7 ultimatE?<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=8983">welhim</a> — Fri Feb 01, 2013 5:37 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[bartbilf]]></name></author>
<updated>2013-01-24T02:14:57+00:00</updated>
<published>2013-01-24T02:14:57+00:00</published>
<id>forum/viewtopic.php?t=695&amp;p=8028#p8028</id>
<link href="forum/viewtopic.php?t=695&amp;p=8028#p8028"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Re: Tutorial 4 Stuck in a Loop no 'local' name]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=695&amp;p=8028#p8028"><![CDATA[
To solve the 'color issue', right click -&gt; Appearance -&gt; Higlighting -&gt; 'Christmas tree'.<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=7382">bartbilf</a> — Thu Jan 24, 2013 2:14 am</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[RageMachine]]></name></author>
<updated>2012-11-14T01:34:37+00:00</updated>
<published>2012-11-14T01:34:37+00:00</published>
<id>forum/viewtopic.php?t=1658&amp;p=3484#p3484</id>
<link href="forum/viewtopic.php?t=1658&amp;p=3484#p3484"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Re: Help understanding memory modification]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=1658&amp;p=3484#p3484"><![CDATA[
<blockquote><div><cite>nwokiller wrote:</cite><br />What OS are you using, if you're on Vista/7 then it's possible the program is using ASLR(If you load it in Olly and the base address changes between runs). Let me know exactly what you want to do here and I'll try and get you going in the right direction. below is a link ti info on ASLR from the blog<br /><!-- m --><a class="postlink" href="http://thelegendofrandom.com/blog/archives/1990">http://thelegendofrandom.com/blog/archives/1990</a><!-- m --><br /></div></blockquote><br /><br />I finally figured it out, I thought I couldn't do it but I just kept working at it all day and I finally got some working code and a better understanding, just by hammering away at the documentation until I figured out what everything meant and got some return values on items. I am very tired, but here is my working(and messy) code:<br /><br /><dl class="codebox"><dt>Code: </dt><dd><code>#include &lt;iostream&gt;<br />#include &lt;windows.h&gt;<br />#include &lt;Psapi.h&gt;<br />#include &lt;tchar.h&gt; <br />#include &lt;TlHelp32.h&gt;<br /><br />#pragma comment( lib, &quot;psapi&quot; )<br />using namespace std;<br /><br />int main() {<br />   MODULEENTRY32 me32;<br />   int pauser,mp,newscore;<br />   HWND hWnd;//handle to a window or form<br />   DWORD dwID; //PID of target<br />   DWORD baseAddress; //32 bit unsigned integer<br />   DWORD finalAddress;<br />   HANDLE hProcess; //handle to the process in question<br />   HANDLE testHandle;<br />   const char* chModName = &quot;Solitaire.exe&quot;; <br />   <br />   hWnd = FindWindow(NULL, &quot;Solitaire&quot;); //find solitaire window<br />   GetWindowThreadProcessId(hWnd, &amp;dwID); //get the process PID based on the Window<br />   hProcess = OpenProcess(PROCESS_ALL_ACCESS, NULL, dwID); //open a handle to the process<br />   cout &lt;&lt; &amp;hProcess &lt;&lt; endl; //print handle to screen<br />   testHandle = CreateToolhelp32Snapshot(TH32CS_SNAPMODULE,dwID);<br />   Module32First(testHandle,&amp;me32); //get first module information<br />   baseAddress =  (DWORD) me32.modBaseAddr; //set base address<br />   baseAddress = baseAddress + 0x00097074; //add offset to get first pointer<br />   cout &lt;&lt; baseAddress &lt;&lt; endl; //show pointer<br />   ReadProcessMemory(hProcess,(LPVOID*)baseAddress,&amp;mp,4,0); //read the target of the pointer<br />   cout &lt;&lt; &quot;INITIAL ADDRESS: &quot; &lt;&lt; hex &lt;&lt; mp &lt;&lt; endl; <br />   baseAddress = mp + 0x2C; //add 2c to the target<br />   cout &lt;&lt; &quot;NEXT ADDRESS: &quot; &lt;&lt; hex &lt;&lt; baseAddress &lt;&lt; endl;<br />   ReadProcessMemory(hProcess,(LPVOID*)baseAddress,&amp;mp,4,0); //read next target<br />   baseAddress = mp;<br />   cout &lt;&lt; &quot;NEXT ADDRESS: &quot; &lt;&lt; hex &lt;&lt; baseAddress &lt;&lt; endl;<br />   baseAddress = baseAddress + 0x18; //add 18 to the next pointer location<br />   cout &lt;&lt; &quot;FINAL ADDRESS: &quot; &lt;&lt; hex &lt;&lt; baseAddress &lt;&lt; endl;<br />   ReadProcessMemory(hProcess,(LPVOID*)baseAddress,&amp;mp,4,0); //show final score<br />   cout &lt;&lt; &quot;Contents of Final Address: &quot; &lt;&lt; mp &lt;&lt; endl;<br />   cout &lt;&lt; &quot;Write new value: &quot; &lt;&lt; endl;<br />   cin &gt;&gt; newscore;<br />   WriteProcessMemory(hProcess,(void*)baseAddress,&amp;newscore,sizeof(newscore),NULL); //write new score<br />   system(&quot;Pause&quot;);<br />   CloseHandle(testHandle);<br />   CloseHandle(hProcess);<br />   return 0;<br />}</code></dd></dl><br /><br />I ended up using CreateToolhelp32Snapshot(TH32CS_SNAPMODULE,dwID) but I feel I am wasting memory to return an entire structure for just one piece of info, but oh well, it works!<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=152">RageMachine</a> — Wed Nov 14, 2012 1:34 am</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[nwokiller]]></name></author>
<updated>2012-11-13T23:16:06+00:00</updated>
<published>2012-11-13T23:16:06+00:00</published>
<id>forum/viewtopic.php?t=1658&amp;p=3483#p3483</id>
<link href="forum/viewtopic.php?t=1658&amp;p=3483#p3483"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Re: Help understanding memory modification]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=1658&amp;p=3483#p3483"><![CDATA[
What OS are you using, if you're on Vista/7 then it's possible the program is using ASLR(If you load it in Olly and the base address changes between runs). Let me know exactly what you want to do here and I'll try and get you going in the right direction. below is a link ti info on ASLR from the blog<br /><!-- m --><a class="postlink" href="http://thelegendofrandom.com/blog/archives/1990">http://thelegendofrandom.com/blog/archives/1990</a><!-- m --><p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=84">nwokiller</a> — Tue Nov 13, 2012 11:16 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[RageMachine]]></name></author>
<updated>2012-11-13T20:27:36+00:00</updated>
<published>2012-11-13T20:27:36+00:00</published>
<id>forum/viewtopic.php?t=1658&amp;p=3479#p3479</id>
<link href="forum/viewtopic.php?t=1658&amp;p=3479#p3479"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Re: Help understanding memory modification]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=1658&amp;p=3479#p3479"><![CDATA[
<blockquote><div><cite>nwokiller wrote:</cite><br />I had a quick look at the exe  you mentioned, as with all exes the image base is in the header and the first &quot;GetModuleHandleA&quot; call retrieves this base in EAX, from there it should just be a matter of adding your offset... reading memory value and patching/storing.<br /></div></blockquote><br /><br />I gave it a try - but its not working. its apparent that my knowledge of WINAPI stops pretty soon - I guess I need to figure out exactly what else goes into it other than just knowing standard C++ - this feels like an entirely different beast. I'll work on it and get back to you once I manage to find some better tutorials\explanations on working with handles and other winAPI functions. I will explain some confusion I have, though. If i use the GetModuleHandleA I get a different address on each run of my program - which shouldn't be the case if the other program has not been touched, correct?<br /><br /><dl class="codebox"><dt>Code: </dt><dd><code>hWnd = FindWindow(NULL, &quot;Solitaire&quot;); //find solitaire window<br />   GetWindowThreadProcessId(hWnd, &amp;dwID); //get the process PID based on the Window<br />   hProcess = OpenProcess(PROCESS_ALL_ACCESS, NULL, dwID); //open a handle to the process<br />   cout &lt;&lt; &amp;hProcess &lt;&lt; endl; //print handle to screen<br />   bHandle = GetModuleHandleA(lpModuleName);<br />   cout &lt;&lt; &amp;bHandle &lt;&lt; endl;<br /></code></dd></dl><br /><br />I get outputted: <br />001AFE90<br />001AFE84<br /><br />Clearly I am misunderstanding something. But if i Loop &amp; Sleep it reads out the same two for the duration of the program, so its unlikely its changing in between runs of my program.<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=152">RageMachine</a> — Tue Nov 13, 2012 8:27 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[markdeleon123]]></name></author>
<updated>2012-11-13T14:24:14+00:00</updated>
<published>2012-11-13T14:24:14+00:00</published>
<id>forum/viewtopic.php?t=876&amp;p=3472#p3472</id>
<link href="forum/viewtopic.php?t=876&amp;p=3472#p3472"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Re: Please tell me what you think...]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=876&amp;p=3472#p3472"><![CDATA[
i agree with kdma.. please provide tuts about reversing the mmorpg applications <img src="forum/images/smilies/icon_e_smile.gif" alt=":)" title="Smile" /><br /><br />and in-depth tuts for unpacking.. i think all of us(newbies) need to master the art of unpacking <img src="forum/images/smilies/icon_e_smile.gif" alt=":)" title="Smile" /><p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=2709">markdeleon123</a> — Tue Nov 13, 2012 2:24 pm</p><hr />
]]></content>
</entry>
<entry>
<author><name><![CDATA[markdeleon123]]></name></author>
<updated>2012-11-13T05:11:47+00:00</updated>
<published>2012-11-13T05:11:47+00:00</published>
<id>forum/viewtopic.php?t=1687&amp;p=3464#p3464</id>
<link href="forum/viewtopic.php?t=1687&amp;p=3464#p3464"/>
<title type="html"><![CDATA[R4ndom's Beginner Guide to Reverse Engineering • Re: about OEP and unpacking tuts]]></title>

<category term="R4ndom's Beginner Guide to Reverse Engineering" scheme="forum/viewforum.php?f=2" label="R4ndom's Beginner Guide to Reverse Engineering"/>
<content type="html" xml:base="forum/viewtopic.php?t=1687&amp;p=3464#p3464"><![CDATA[
ok sir. thank you .. maybe experience is all that i need <img src="forum/images/smilies/icon_e_smile.gif" alt=":)" title="Smile" /> thanks for your effort answering my concerns<p>Statistics: Posted by <a href="/forum/memberlist.php?mode=viewprofile&amp;u=2709">markdeleon123</a> — Tue Nov 13, 2012 5:11 am</p><hr />
]]></content>
</entry>
</feed>