<?php
/*
 * Builds the Lift installation guide pages from the fragments in this folder.
 * Run from anywhere:  php lift/guide/_build/build.php
 * Folders that start with "_" are not published by GitHub Pages.
 *
 * Fragments (steps/<slug>.html): the step text, then <!--stuck--> and the "stuck?" questions.
 * Inside a fragment:
 *   [[shot:N]]     screenshot N (img/shot-N.png). Until the file exists, a placeholder is shown.
 *   [[video:name]] short video (img/name.mp4). Until the file exists, a placeholder is shown.
 */

$root = dirname( __DIR__ );          // lift/guide
$tpl  = file_get_contents( __DIR__ . '/template.html' );
$base = '/lift/guide/wordpress/';

// Steps, in order. The slug is the step's address: /lift/guide/wordpress/<slug>/ (the plugin links to it).
$groups = array(
	'install' => 'התקנה',
	'wizard'  => 'כמה שאלות על העסק',
	'turn-on' => 'הפעלה',
	'end'     => 'סיום',
);
$steps = array(
	array( 'before',   'לפני שמתחילים',                 'install' ),
	array( 'login',    'נכנסים לניהול האתר',             'install' ),
	array( 'upload',   'מעלים את התוסף',                 'install' ),
	array( 'activate', 'מפעילים את Lift',                'install' ),
	array( 'business', 'העסק שלכם',                      'wizard' ),
	array( 'people',   'אנשים ומקום',                    'wizard' ),
	array( 'links',    'קישורים',                        'wizard' ),
	array( 'pages',    'דפים חשובים',                    'wizard' ),
	array( 'llms',     'llms.txt: קוראים ומפרסמים',      'turn-on' ),
	array( 'schema',   'פרטי עסק לגוגל ול-AI',           'turn-on' ),
	array( 'crawlers', 'סורקים ומפת אתר',                'turn-on' ),
	array( 'score',    'ציון מוכנות: מה מתקנים קודם',    'turn-on' ),
	array( 'done',     'סיימתם!',                        'end' ),
);

// Screenshots (img/shot-N.png or .jpg): alt text, and marks as
// [left %, top %, width %, height %, label] measured on the image. The label is the number of the
// matching instruction on the page ('' for no number).
$shots = array(
	1  => array( 'מסך הכניסה לניהול האתר', array( array( 26, 36.5, 48, 8.6, 2 ), array( 26, 52.8, 48, 8.6, 2 ), array( 26, 64.4, 12.6, 8.6, 2 ) ) ),
	2  => array( 'תוספים ← הוספת תוסף, עם הכפתור "העלאת תוסף" למעלה', array( array( 82.6, 39.6, 16.2, 14.8, 1 ), array( 58.9, 6.6, 9.8, 5.6, 2 ) ) ),
	3  => array( 'אזור בחירת הקובץ, אחרי הלחיצה על "העלאת תוסף"', array( array( 49.5, 31.4, 10.2, 5.8, 3 ), array( 22.8, 30.6, 11.2, 7.4, 3 ) ) ),
	4  => array( 'סוף ההתקנה, עם הכפתור "הפעל תוסף"', array( array( 72.6, 27.6, 10.4, 7.8, 1 ) ) ),
	5  => array( 'רשימת התוספים: Lift מופעל, ובתפריט בצד מופיע Lift', array( array( 64, 41.6, 14, 5.8, 2 ), array( 82.6, 69.8, 16.2, 5.2, 2 ) ) ),
	15 => array( 'מסך הפתיחה של Lift, עם הכפתור "מתחילים!"', array( array( 65.2, 34.2, 13, 7.6, 1 ) ) ),
	6  => array( 'האשף, מסך "העסק שלכם"', array( array( 43.4, 41, 34.8, 6.4, 2 ), array( 8.2, 57, 70, 12, 3 ), array( 71, 75, 7.6, 7, 4 ) ) ),
	7  => array( 'האשף, מסך "אנשים ומקום": שם, תפקיד, עיר ואזור', array( array( 43.4, 41, 34.8, 6.4, 1 ), array( 43.4, 69.2, 34.8, 6.6, 2 ), array( 43.4, 82, 34.8, 6.4, 2 ) ) ),
	16 => array( 'המשך המסך "אנשים ומקום": שפות, טלפון ואימייל', array( array( 70.6, 31.4, 8, 18.2, 3 ), array( 43.4, 56.5, 34.8, 6.6, 4 ), array( 71, 80.6, 7.6, 7.2, 5 ) ) ),
	8  => array( 'האשף, מסך "קישורים"', array( array( 43.4, 46.1, 34.8, 6.6, 1 ), array( 43.4, 87.1, 34.8, 6.6, 2 ) ) ),
	9  => array( 'האשף, מסך "דפים חשובים"', array( array( 64.4, 39.4, 14.2, 5.2, 1 ), array( 68.4, 49.4, 10, 6.6, 3 ) ) ),
	10 => array( 'מסך llms.txt, עם הכפתור "פרסום"', array( array( 8.2, 28.8, 70, 58.2, 2 ), array( 71, 87.8, 7.6, 6.8, 3 ) ) ),
	11 => array( 'מסך "פרטי עסק לגוגל ול-AI", עם הכפתור "הפעלה"', array( array( 8.2, 33, 70, 43.6, 2 ), array( 70.6, 84.2, 8, 7, 3 ) ) ),
	12 => array( 'מסך "סורקים ומפת אתר": כל הסורקים יכולים לקרוא', array( array( 55.2, 29.4, 23.6, 32.8, 2 ) ) ),
	17 => array( 'המשך המסך "סורקים ומפת אתר": מפת האתר', array( array( 59.6, 42.2, 18.6, 7, 3 ) ) ),
	13 => array( 'מסך "ציון מוכנות" אחרי הבדיקה', array( array( 7.4, 33.6, 70.8, 15.6, 2 ), array( 53.4, 57, 24.8, 10.4, 4 ) ) ),
	14 => array( 'מסך "סקירה", עם הרשימה "איך מתחילים"', array( array( 50.4, 29.4, 28, 19.6, '' ) ) ),
);

$help = '<div class="help"><b>עדיין לא מסתדר?</b> בהתקנה העצמית אין תמיכה אישית, ולכן המדריך הזה מנסה לענות על הכול. '
	. 'אם בכל זאת נתקעתם: ב-<b>Lift עם ליווי</b> פאני מתקינה יחד איתכם (1,200 ש"ח, ומי שכבר קנו משלמים רק את ההפרש). '
	. '<a href="https://wa.me/972547892320?text=' . rawurlencode( 'היי פאני, אשמח לשמוע על Lift עם ליווי' ) . '" target="_blank" rel="noopener">לפרטים על ליווי</a></div>';

function e( $s ) { return htmlspecialchars( $s, ENT_QUOTES, 'UTF-8' ); }

function expand( $html, $root, $shots ) {
	$html = preg_replace_callback( '/\[\[shot:(\d+)\]\]/', function ( $m ) use ( $root, $shots ) {
		$n   = (int) $m[1];
		$alt = isset( $shots[ $n ] ) ? $shots[ $n ][0] : '';
		$rel = 'img/shot-' . $n . '.png';
		if ( ! file_exists( $root . '/' . $rel ) ) {
			$rel = 'img/shot-' . $n . '.jpg';
		}
		if ( ! file_exists( $root . '/' . $rel ) ) {
			return '<figure class="shot"><div class="ph"><b>צילום מסך ' . $n . '</b>' . e( $alt ) . '</div></figure>';
		}
		list( $w, $h ) = getimagesize( $root . '/' . $rel );
		$marks = '';
		foreach ( $shots[ $n ][1] as $mk ) {
			$label  = isset( $mk[4] ) && '' !== $mk[4] ? '<b>' . $mk[4] . '</b>' : '';
			$marks .= sprintf( '<span class="mark" style="left:%s%%;top:%s%%;width:%s%%;height:%s%%" aria-hidden="true">%s</span>', $mk[0], $mk[1], $mk[2], $mk[3], $label );
		}
		$src = '/lift/guide/' . $rel;
		return '<figure class="shot"><div class="frame"><img src="' . $src . '" alt="' . e( $alt ) . '" width="' . $w . '" height="' . $h . '" loading="lazy">' . $marks . '</div>'
			. '<a class="zoom" href="' . $src . '" target="_blank">פתיחת התמונה בגודל מלא</a></figure>';
	}, $html );
	$html = preg_replace_callback( '/\[\[video:([a-z0-9-]+)\]\]/', function ( $m ) use ( $root ) {
		$rel = 'img/' . $m[1] . '.mp4';
		if ( ! file_exists( $root . '/' . $rel ) ) {
			return '<div class="video"><div class="ph"><b>סרטון קצר</b>כאן יבוא סרטון של חצי דקה שמראה את הצעד הזה</div></div>';
		}
		return '<div class="video"><video src="/lift/guide/' . $rel . '" controls preload="metadata" playsinline></video></div>';
	}, $html );
	return $html;
}

function steps_list( $steps, $groups, $base, $current = '' ) {
	$out = '<ol class="steps">';
	$g   = '';
	foreach ( $steps as $s ) {
		if ( $s[2] !== $g ) {
			$g    = $s[2];
			$out .= '<li class="grp" aria-hidden="true">' . e( $groups[ $g ] ) . '</li>';
		}
		$cls  = $s[0] === $current ? ' class="here"' : '';
		$cur  = $s[0] === $current ? ' aria-current="step"' : '';
		$out .= '<li data-s="' . $s[0] . '"' . $cls . '><a href="' . $base . $s[0] . '/"' . $cur . '>' . e( $s[1] ) . '</a></li>';
	}
	return $out . '</ol>';
}

function page( $tpl, $path, $title, $desc, $crumbs, $main ) {
	return strtr( $tpl, array(
		'{{TITLE}}'  => e( $title ),
		'{{PATH}}'   => $path,
		'{{DESC}}'   => e( $desc ),
		'{{CRUMBS}}' => $crumbs,
		'{{MAIN}}'   => $main,
	) );
}

function write( $file, $html ) {
	if ( ! is_dir( dirname( $file ) ) ) {
		mkdir( dirname( $file ), 0777, true );
	}
	file_put_contents( $file, $html );
	echo 'wrote ' . $file . "\n";
}

$c_home  = '<a href="/">זרמומיה</a> ‹ ';
$c_guide = '<a href="/lift/guide/">מדריך התקנה ל-Lift</a>';
$c_wp    = '<a href="' . $base . '">וורדפרס</a>';

// 1. Platform choice.
$main = expand( file_get_contents( __DIR__ . '/pages/platform.html' ), $root, $shots );
write( $root . '/index.html', page( $tpl, '/lift/guide/', 'מדריך התקנה ל-Lift | זרמומיה',
	'מתקינים את Lift לבד, צעד אחר צעד, בעברית ובלי ידע טכני. בוחרים פלטפורמה ומתחילים.',
	$c_home . '<span>מדריך התקנה ל-Lift</span>', $main ) );

// 2. WordPress overview.
$main = file_get_contents( __DIR__ . '/pages/wordpress.html' );
$main = str_replace( '[[steps]]', steps_list( $steps, $groups, $base ), $main );
$main = str_replace( '[[first]]', $base . $steps[0][0] . '/', $main );
$main = str_replace( '[[count]]', (string) count( $steps ), $main );
write( $root . '/wordpress/index.html', page( $tpl, $base, 'מדריך התקנה: Lift לוורדפרס | זרמומיה',
	'מתקינים את Lift בוורדפרס לבד, ב-' . count( $steps ) . ' צעדים קצרים, בערך 15 דקות.',
	$c_home . $c_guide . ' ‹ <span>וורדפרס</span>', $main ) );

// 3. Steps.
$total = count( $steps );
foreach ( $steps as $i => $s ) {
	list( $slug, $title ) = $s;
	$n    = $i + 1;
	$src  = file_get_contents( __DIR__ . '/steps/' . $slug . '.html' );
	$part = explode( '<!--stuck-->', $src );
	$body = expand( trim( $part[0] ), $root, $shots );
	$stk  = isset( $part[1] ) ? trim( $part[1] ) : '';

	$prog = '<div class="prog" data-step="' . $slug . '"><div class="prog-top"><span>צעד ' . $n . ' מתוך ' . $total . '</span>'
		. '<details class="all"><summary>כל הצעדים</summary>' . steps_list( $steps, $groups, $base, $slug ) . '</details></div>'
		. '<div class="bar" role="progressbar" aria-label="התקדמות במדריך" aria-valuemin="1" aria-valuemax="' . $total . '" aria-valuenow="' . $n . '"><i style="width:' . round( 100 * $n / $total ) . '%"></i></div></div>';

	$stuck = '<details class="stuck"><summary>נתקעתם?</summary><div class="in">' . $stk . $help . '</div></details>';

	$back = $i > 0
		? '<a class="back" href="' . $base . $steps[ $i - 1 ][0] . '/">→ לצעד הקודם</a>'
		: '<a class="back" href="' . $base . '">→ לרשימת הצעדים</a>';
	$next = $i < $total - 1
		? '<a class="btn" href="' . $base . $steps[ $i + 1 ][0] . '/" data-done="' . $slug . '">סיימתי, הלאה ←</a>'
		: '<a class="btn teal" href="' . $base . '" data-done="' . $slug . '">סיימתי ✓</a>';

	$main = $prog . '<h1>' . e( $title ) . '</h1>' . $body . $stuck . '<div class="next">' . $back . $next . '</div>';

	write( $root . '/wordpress/' . $slug . '/index.html', page( $tpl, $base . $slug . '/',
		'צעד ' . $n . ': ' . $title . ' | מדריך התקנה ל-Lift',
		'מדריך התקנה ל-Lift בוורדפרס, צעד ' . $n . ' מתוך ' . $total . ': ' . $title . '.',
		$c_home . $c_guide . ' ‹ ' . $c_wp . ' ‹ <span>צעד ' . $n . '</span>', $main ) );
}
