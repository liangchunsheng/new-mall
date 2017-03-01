<?php



默认情况下SESSION保存在服务器的硬盘中，没有特别的存储长度限制，理论上可以存储任何数据，但并不建议任何数据都保存在SESSION中，原因不说了（考虑一下用户数及其庞大的情况下，每访问一个php文件，就要读取SEESION，特别是SEESION写入内存的情况下。），当然也可以写入memcache，甚至单独的SESSION服务器。

SESSION通常用来保存与用户信息相关的： 1. 身份信息、登陆状态 2. 用户的个性配置、权限列表 3. 其他的一些通用数据（比如购物车）

我通常把通用的、频繁存取的、小数据量的跟用户相关的数据放入SEESION，视场景而定，我手头的一个项目，是把模块的信息（属性、菜单、结合权限生成栏目列表）写入SEESION的。
当我们执行session_start()之后，就会生成一个sessionID，保存在$_COOKIE变量中(此时，'该sessionID并没有保存到客户端，他仅仅存在于会话之中')。
同时服务器端默认会在/tmp目录下建立一个session文件，文件名是用前缀“sess_”再加上当前的sessionID组合而成的，我暂且把他叫做sess文件吧。
cookie机制：
当使用cookie机制的时候，在sess文件中没有保存任何数据。而cookie中的数据，实际上是通过setcookie()的方法保存在客户端的。
并且，该方法只将指定的数据保存到了客户端，而sessionID并没有被保存到客户端。这个时候sessionID依然存在于会话之中。
当重新打开浏览器之后，我们发现当前的sessionID实际上已经不是之前的sessionID了，
但是cookie中的数据并不受影响，我们仍然可以通过$_COOKIE变量来获取cookie的值。
一般的session机制：
当使用session机制的时候，所有$_SESSION中的数据实际上是保存在了sess文件中，当我们在页面间跳转的时候，由于sessionID不变,
php会通过当前的sessionID找到sess文件，然后取出其中的数据，因此我们就可以通过$_SESSION变量来获session的值。
等到重启浏览器之后，由于sessionID变化了，所以也就取不到$_SESSION中的值了。
上面为什么说是一般的session机制呢，因为我们可以用一个php的函数：session_set_cookie_params()来实现cookie的机制。详情如下：
CODE:
// 取出当前的sessionID的设置
$cookieParams = session_get_cookie_params();
// 修改当前的sessionID的设置
session_set_cookie_params(
    3600,// 设置sessionID在cookie中保存的时长
    $cookieParams['path'],
    $cookieParams['domain'],
    $cookieParams['secure']
    );
// 重新生成一个新的sessionID
session_regenerate_id(true);
// 执行session_start()
// 注意，此时的session_start跟平时不一样的地方是，这个时候启动session的同时，他会把当前的sessionID保存到客户端的cookie中
session_start();
通过以上方法，这样在就算重启了浏览器，只要客户端的cookie文件不过期，
php会自动(不需要人工获取)通过cookie文件获取到上次访问时的sessionID，因此依然可以获取到之前设置的$_SESSION数据
。
有的同学看到这里可能就会问了。sess文件不是有存活期的吗，生存期过了会怎么样？
这个我还没测试，我目前估计当存活期过了之后，用这个方法就取不到session中的值了。
因为sess文件都不存在了嘛。不过我们可以通过控制sess文件的存活期(修改php.ini中的session.gc_maxlifetime选项，默认是1440秒)，来延长$_SESSION的访问。
但是我并不建议这么作，因为php的sess文件默认都是放在一个目录下的。也就是说默认情况下24分钟后，该sess文件就会被清理掉了。
如果我们想用session实现cookie，那么就得让sess文件一直保留。
假设一台服务器一天有10万个ip访问，那么服务器上就至少有不下于10万个sess文件。
如果你将sess文件保存的时间过长，甚至永久保存的话。
那么就会出问题了，同一个文件夹下堆积了过多的文件就会造成服务器访问该文件夹的时候出问题。
我们的服务器曾经发生过一次这种事故，具体堆积了多少个sess文件我忘记了。
但是我清楚的记得，我在tmp文件夹下甚至连执行删除的命令都会让终端死掉。最后是写了一个shell，慢慢删才删掉的。
当然重启服务器也可以，不过为了保证服务器的不间断，我们没有这么作。
第一种方法 在cmd中回车开开dos窗口 
输入：del C:windowstempsess* /s /q
第二种方法创建个批处理，使用计划任务多次定时调用即可!将下面文件保存后修改成 清理TEMP文件夹.bat
@echo off
del /f /s /q c:windowstemp*.*exit
phpmyadmin在使用过程中经常出现“登陆超时（1440秒未活动），请重新登录”，很烦
解决方法如下：
修改php.ini，找到
session.gc_maxlifetime = 1440
将数值改大就行了，然后使之生效
试验了一下，结果不好使。
最终解决方案：
找到 phpMyAdmin / libraries / config.default.php 文件，打开，修改
$cfg['LoginCookieValidity'] = 1440;
将1440修改成更大的值即可。
注意：$cfg['LoginCookieValidity']的值不能大于php.ini里的session.gc_maxlifetime的值，否则phpmyadmin 里会出现“您的 PHP配置参数 session.gc_maxlifetime (外链，英文) 短于您在 phpMyAdmin 中设置的 Cookies 有效期，因此您的登录会话有效期将会比您在 phpMyAdmin 中设置的时间要更短。”错误。
PHP中的session有效期默认是1440秒（24分钟）【weiweiok 注：php5里默认的是180分】，也就是说，客户端超过24分钟没有刷新，当前session就会失效。很明显，这是不能满足需要的。
一个已知管用的方法是，使用session_set_save_handler，接管所有的session管理工作，一般是把session信息存储到数据库，这样可以通过SQL语句来删除所有过期的session，精确地控制session的有效期。这也是基于PHP的大型网站常用的方法。但是，一般的小型网站，似乎没有必要这么劳师动众。
但是一般的Session的生命期有限，如果用户关闭了浏览器，就不能保存Session的变量了！那么怎么样可以实现Session的永久生命期呢？
大家知道，Session储存在服务器端，根据客户端提供的SessionID来得到这个用户的文件，然后读取文件，取得变量的值，SessionID可以使用客户端的Cookie或者Http1.1协议的Query_String（就是访问的URL的“?”后面的部分）来传送给服务器，然后服务器读取Session的目录……
要实现Session的永久生命期，首先需要了解一下php.ini关于Session的相关设置（打开php.ini文件，在“[Session]”部分）：
1、session.use_cookies：默认的值是“1”，代表SessionID使用Cookie来传递，反之就是使用Query_String来传递；
2、session.name：这个就是SessionID储存的变量名称，可能是Cookie，也可能是Query_String来传递，默认值是“PHPSESSID”；                      
3、session.cookie_lifetime：这个代表SessionID在客户端Cookie储存的时间，默认是0，代表浏览器一关闭SessionID就作废……就是因为这个所以Session不能永久使用！
4、session.gc_maxlifetime：这个是Session数据在服务器端储存的时间，如果超过这个时间，那么Session数据就自动删除！
还有很多的设置，不过和本文相关的就是这些了，下面开始讲使用永久Session的原理和步骤。
前面说过，服务器通过SessionID来读取Session的数据，但是一般浏览器传送的SessionID在浏览器关闭后就没有了，那么我们只需要人为的设置SessionID并且保存下来，不就可以……
如果你拥有服务器的操作权限，那么设置这个非常非常的简单，只是需要进行如下的步骤：
1、把“session.use_cookies”设置为1，打开Cookie储存SessionID，不过默认就是1，一般不用修改；
2、把“session.cookie_lifetime”改为正无穷（当然没有正无穷的参数，不过999999999和正无穷也没有什么区别）;
3、把“session.gc_maxlifetime”设置为和“session.cookie_lifetime”一样的时间；
在PHP的文档中明确指出，设定session有效期的参数是session.gc_maxlifetime。可以在php.ini文件中，或者通过ini_set()函数来修改这一参数。问题在于，经过多次测试，修改这个参数基本不起作用，session有效期仍然保持24分钟的默认值。
由于PHP的工作机制，它并没有一个daemon线程，来定时地扫描session信息并判断其是否失效。当一个有效请求发生时，PHP会根据全局变量session.gc_probability/session.gc_divisor（同样可以通过php.ini或者ini_set()函数来修改）的值，来决定是否启动一个GC（Garbage Collector）。
默认情况下，session.gc_probability ＝ 1，session.gc_divisor ＝100，也就是说有1%的可能性会启动GC。GC的工作，就是扫描所有的session信息，用当前时间减去session的最后修改时间（modified date），同session.gc_maxlifetime参数进行比较，如果生存时间已经超过gc_maxlifetime，就把该session删除。
到此为止，工作一切正常。那为什么会发生gc_maxlifetime无效的情况呢？
在默认情况下，session信息会以文本文件的形式，被保存在系统的临时文件目录中。在Linux下，这一路径通常为tmp，在 Windows下通常为C:WindowsTemp。当服务器上有多个PHP应用时，它们会把自己的session文件都保存在同一个目录中。同样地，这些PHP应用也会按一定机率启动GC，扫描所有的session文件。
问题在于，GC在工作时，并不会区分不同站点的session。举例言之，站点A的gc_maxlifetime设置为2小时，站点B的 gc_maxlifetime设置为默认的24分钟。当站点B的GC启动时，它会扫描公用的临时文件目录，把所有超过24分钟的session文件全部删除掉，而不管它们来自于站点A或B。这样，站点A的gc_maxlifetime设置就形同虚设了。
找到问题所在，解决起来就很简单了。修改session.save_path参数，或者使用session_save_path()函数，把保存session的目录指向一个专用的目录，gc_maxlifetime参数工作正常了。
严格地来说，这算是PHP的一个bug？
还有一个问题就是，gc_maxlifetime只能保证session生存的最短时间，并不能够保存在超过这一时间之后session信息立即会得到删除。因为GC是按机率启动的，可能在某一个长时间内都没有被启动，那么大量的session在超过gc_maxlifetime以后仍然会有效。
解决这个问题的一个方法是，把session.gc_probability/session.gc_divisor的机率提高，如果提到100%，就会彻底解决这个问题，但显然会对性能造成严重的影响。另一个方法是自己在代码中判断当前session的生存时间，如果超出了 gc_maxlifetime，就清空当前session。
但是如果你没有服务器的操作权限，那就比较麻烦了，你需要通过PHP程序改写SessionID来实现永久的Session数据保存。查查php.net的函数手册，可以见到有“session_id”这个函数：如果没有设置参数，那么将返回当前的SessionID，如果设置了参数，就会将当前的SessionID设置为给出的值……
只要利用永久性的Cookie加上“session_id”函数，就可以实现永久Session数据保存了！
但是为了方便，我们需要知道服务器设置的“session.name”，但是一般用户都没有权限查看服务器的php.ini设置，不过PHP提供了一个非常好的函数“phpinfo”，利用这个可以查看几乎所有的PHP信息！
------------------------------------------------------------------------------------
------------------------------------------------------------------------------------
打开编辑器，输入上面的代码，然后在浏览器中运行这个程序，会见到PHP的相关信息（如图1所示）。其中有一项“session.name”的参数，这个就是我们需要的服务器“session.name”，一般是“PHPSESSID”。
记下了SessionID的名称后，我们就可以实现永久的Session数据储存了！
session_start();
ini_set('session.save_path','/tmp/');
//6个钟头
ini_set('session.gc_maxlifetime',21600);
//保存一天
$lifeTime = 24 * 3600;
setcookie(session_name(), session_id(), time() + $lifeTime, "/");
后记：
其实真正的永久储存是不可能的，因为Cookie的保存时间有限，而服务器的空间也有限……但是对于一些需要保存时间比较长的站点，以上方法就已经足够了！
把session放入mysql的Example:
数据库里建表：session ( sesskey varchar32 , expiry int11 , value longtext)
code:
代码执行前已经连接数据库了。
define('STORE_SESSIONS','mysql');
if (STORE_SESSIONS == 'mysql') {
if (!$SESS_LIFE = get_cfg_var('session.gc_maxlifetime')) {
$SESS_LIFE = 1440;
}
function _sess_open($save_path, $session_name) {
// 如果没有连接数据库，可以在此执行mysql_pconnect,mysql_select_db
return true;
}
function _sess_close() {
return true;
}
function _sess_read($key) {
$value_query = mysql_query("select value from sessions where sesskey = '" .addslashes($key) . "' and expiry > '" . time() . "'");
$value = mysql_fetch_array($value_query);
if (isset($value['value'])) {
return $value['value'];
}
return false;
}
function _sess_write($key, $val) {
global $SESS_LIFE;
$expiry = time() + $SESS_LIFE;
$value = $val;
$check_query = mysql_query("select count(*) as total from sessions where sesskey = '" . addslashes($key) . "'");
$check = mysql_fetch_array($check_query);
if ($check['total'] > 0) {
return mysql_query("update sessions set expiry = '" . addslashes($expiry) . "', value = '" . addslashes($value) . "' where sesskey = '" . addslashes($key) . "'");
} else {
return mysql_query("insert into sessions values ('" . addslashes($key) . "', '" . addslashes($expiry) . "', '" . addslashes($value) . "')");
}
}
function _sess_destroy($key) {
return mysql_query("delete from sessions where sesskey = '" . addslashes($key) . "'");
}
function _sess_gc($maxlifetime) {
mysql_query("delete from sessions where expiry < '" . time() . "'");
return true;
}
session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
}
danoo_session_name( 'dtvSid' );
danoo_session_save_path(SESSION_WRITE_DIRECTORY);
还是有点不明白，open,write那些参数哪里来的。
修改php.ini配置的两个常用函数：
get_cfg_var('session.gc_maxlifetime') : 取得session.gc_maxlifetime的值
ini_set('session.cookie_lifetime','0') : 设置session.cookie_lifetime的值为0。
    希望这篇PHP中session有效期删除sess文件的方法的文章能够对您有所帮助，如果您觉得这篇网站维护教程有用的话，别忘了推荐给您的朋友哦！如果您有好的经验方法，不妨拿出来和大家一起分享：假如每个人都拿出一个经验，那么我们都将额外的获取一堆他人的经验。*/