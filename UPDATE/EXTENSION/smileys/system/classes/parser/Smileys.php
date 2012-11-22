<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 *
 * BIGACE is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * BIGACE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @package bigace.classes
 * @subpackage parser
 */

/**
 * Class used for parsing markup code and replacing abbreviations into HTML IMG Smileys.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage parser
 */
class Smileys
{

	/**
	 * Smileys like:
	 * :smile: => smile.gif
	 *
	 * @return array mappings of smileys and image names
	 */
	static function getTextSmileys() {
		return array(
		    ":smile:" => "smile2.gif",
			":lol:" => "laugh.gif",
		    ":w00t:" => "w00t.gif",
			":weep:" => "weep.gif",
		    ":innocent:" => "innocent.gif",
		    ":whistle:" => "whistle.gif",
		    ":unsure:" => "unsure.gif",
		    ":closedeyes:" => "closedeyes.gif",
		    ":cool:" => "cool2.gif",
		    ":fun:" => "fun.gif",
		    ":thumbsup:" => "thumbsup.gif",
		    ":thumbsdown:" => "thumbsdown.gif",
		    ":blush:" => "blush.gif",
		    ":unsure:" => "unsure.gif",
		    ":yes:" => "yes.gif",
		    ":no:" => "no.gif",
		    ":love:" => "love.gif",
		    ":?:" => "question.gif",
		    ":!:" => "excl.gif",
		    ":idea:" => "idea.gif",
		    ":arrow:" => "arrow.gif",
		    ":arrow2:" => "arrow2.gif",
		    ":hmm:" => "hmm.gif",
		    ":hmmm:" => "hmmm.gif",
		    ":huh:" => "huh.gif",
		    ":geek:" => "geek.gif",
		    ":look:" => "look.gif",
		    ":rolleyes:" => "rolleyes.gif",
		    ":kiss:" => "kiss.gif",
		    ":shifty:" => "shifty.gif",
		    ":blink:" => "blink.gif",
		    ":smartass:" => "smartass.gif",
		    ":sick:" => "sick.gif",
		    ":crazy:" => "crazy.gif",
		    ":wacko:" => "wacko.gif",
		    ":alien:" => "alien.gif",
		    ":wizard:" => "wizard.gif",
		    ":wave:" => "wave.gif",
		    ":wavecry:" => "wavecry.gif",
		    ":baby:" => "baby.gif",
		    ":angry:" => "angry.gif",
		    ":ras:" => "ras.gif",
		    ":sly:" => "sly.gif",
		    ":devil:" => "devil.gif",
		    ":evil:" => "evil.gif",
		    ":evilmad:" => "evilmad.gif",
		    ":sneaky:" => "sneaky.gif",
		    ":axe:" => "axe.gif",
		    ":slap:" => "slap.gif",
		    ":wall:" => "wall.gif",
		    ":rant:" => "rant.gif",
		    ":jump:" => "jump.gif",
		    ":yucky:" => "yucky.gif",
		    ":nugget:" => "nugget.gif",
		    ":smart:" => "smart.gif",
		    ":shutup:" => "shutup.gif",
		    ":shutup2:" => "shutup2.gif",
		    ":crockett:" => "crockett.gif",
		    ":zorro:" => "zorro.gif",
		    ":snap:" => "snap.gif",
		    ":beer:" => "beer.gif",
		    ":beer2:" => "beer2.gif",
		    ":drunk:" => "drunk.gif",
		    ":strongbench:" => "strongbench.gif",
		    ":weakbench:" => "weakbench.gif",
		    ":dumbells:" => "dumbells.gif",
		    ":music:" => "music.gif",
		    ":stupid:" => "stupid.gif",
		    ":dots:" => "dots.gif",
		    ":offtopic:" => "offtopic.gif",
		    ":spam:" => "spam.gif",
		    ":oops:" => "oops.gif",
		    ":lttd:" => "lttd.gif",
		    ":please:" => "please.gif",
		    ":sorry:" => "sorry.gif",
		    ":hi:" => "hi.gif",
		    ":yay:" => "yay.gif",
		    ":cake:" => "cake.gif",
		    ":hbd:" => "hbd.gif",
		    ":band:" => "band.gif",
		    ":punk:" => "punk.gif",
		    ":rofl:" => "rofl.gif",
		    ":bounce:" => "bounce.gif",
		    ":mbounce:" => "mbounce.gif",
		    ":thankyou:" => "thankyou.gif",
		    ":gathering:" => "gathering.gif",
		    ":hang:" => "hang.gif",
		    ":chop:" => "chop.gif",
		    ":rip:" => "rip.gif",
		    ":whip:" => "whip.gif",
		    ":judge:" => "judge.gif",
		    ":chair:" => "chair.gif",
		    ":tease:" => "tease.gif",
		    ":box:" => "box.gif",
		    ":boxing:" => "boxing.gif",
		    ":guns:" => "guns.gif",
		    ":shoot:" => "shoot.gif",
		    ":shoot2:" => "shoot2.gif",
		    ":flowers:" => "flowers.gif",
		    ":wub:" => "wub.gif",
		    ":lovers:" => "lovers.gif",
		    ":kissing:" => "kissing.gif",
		    ":kissing2:" => "kissing2.gif",
		    ":console:" => "console.gif",
		    ":group:" => "group.gif",
		    ":hump:" => "hump.gif",
		    ":hooray:" => "hooray.gif",
		    ":happy2:" => "happy2.gif",
		    ":clap:" => "clap.gif",
		    ":clap2:" => "clap2.gif",
		    ":weirdo:" => "weirdo.gif",
		    ":yawn:" => "yawn.gif",
		    ":bow:" => "bow.gif",
		    ":dawgie:" => "dawgie.gif",
		    ":cylon:" => "cylon.gif",
		    ":book:" => "book.gif",
		    ":fish:" => "fish.gif",
		    ":mama:" => "mama.gif",
		    ":pepsi:" => "pepsi.gif",
		    ":medieval:" => "medieval.gif",
		    ":rambo:" => "rambo.gif",
		    ":ninja:" => "ninja.gif",
		    ":hannibal:" => "hannibal.gif",
		    ":party:" => "party.gif",
		    ":snorkle:" => "snorkle.gif",
		    ":evo:" => "evo.gif",
		    ":king:" => "king.gif",
		    ":chef:" => "chef.gif",
		    ":mario:" => "mario.gif",
		    ":pope:" => "pope.gif",
		    ":fez:" => "fez.gif",
		    ":cap:" => "cap.gif",
		    ":cowboy:" => "cowboy.gif",
		    ":pirate:" => "pirate.gif",
		    ":pirate2:" => "pirate2.gif",
		    ":rock:" => "rock.gif",
		    ":cigar:" => "cigar.gif",
		    ":icecream:" => "icecream.gif",
		    ":oldtimer:" => "oldtimer.gif",
		    ":trampoline:" => "trampoline.gif",
		    ":banana:" => "bananadance.gif",
		    ":smurf:" => "smurf.gif",
		    ":yikes:" => "yikes.gif",
		    ":osama:" => "osama.gif",
		    ":saddam:" => "saddam.gif",
		    ":santa:" => "santa.gif",
		    ":indian:" => "indian.gif",
		    ":pimp:" => "pimp.gif",
		    ":nuke:" => "nuke.gif",
		    ":jacko:" => "jacko.gif",
		    ":ike:" => "ike.gif",
		    ":greedy:" => "greedy.gif",
		    ":super:" => "super.gif",
		    ":wolverine:" => "wolverine.gif",
		    ":spidey:" => "spidey.gif",
		    ":spider:" => "spider.gif",
		    ":bandana:" => "bandana.gif",
		    ":construction:" => "construction.gif",
		    ":sheep:" => "sheep.gif",
		    ":police:" => "police.gif",
		    ":detective:" => "detective.gif",
		    ":bike:" => "bike.gif",
		    ":fishing:" => "fishing.gif",
		    ":clover:" => "clover.gif",
		    ":horse:" => "horse.gif",
		    ":shit:" => "shit.gif",
		    ":soldiers:" => "soldiers.gif",
			":Boozer:" => "alcoholic.gif",
		    ":deadhorse:" => "deadhorse.gif",
		    ":spank:" => "spank.gif",
		    ":yoji:" => "yoji.gif",
		    ":locked:" => "locked.gif",
		    ":grrr:" => "angry.gif",
			":sleeping:" => "sleeping.gif",
		    ":clown:" => "clown.gif",
		    ":mml:" => "mml.gif",
		    ":rtf:" => "rtf.gif",
		    ":morepics:" => "morepics.gif",
		    ":rb:" => "rb.gif",
		    ":rblocked:" => "rblocked.gif",
		    ":maxlocked:" => "maxlocked.gif",
		    ":hslocked:" => "hslocked.gif",
		    ":wink:" => "wink.gif",
		);
	}

	/**
	 * Smileys like:
	 * :-) => smile.gif
	 *
	 * @return array mappings of smileys and image names
	 */
	static function getSmileys() {
		return array(
			":-)" => "smile1.gif",
		    ":-D" => "grin.gif",
		    ":-P" => "tongue.gif",
		    ";-)" => "wink.gif",
		    ":-|" => "noexpression.gif",
		    ":-/" => "confused.gif",
		    ":-(" => "sad.gif",
		    ":'-(" => "cry.gif",
		    ":-O" => "ohmy.gif",
		    ":o)" => "clown.gif",
		    "8-)" => "cool1.gif",
		    "|-)" => "sleeping.gif",
			":)" => "smile1.gif",
		    ";)" => "wink.gif",
		    ":D" => "grin.gif",
		    ":P" => "tongue.gif",
		    ":(" => "sad.gif",
		    ":'(" => "cry.gif",
		    ":|" => "noexpression.gif",
		    "8-)" => "cool1.gif",
		    "O:-" => "innocent.gif",
		    "-_-" => "unsure.gif",
		    );
	}

    /**
     * Parses any Markup Code for Smileys.
     */
    static function parseCode($markupcode, $textual = false) {
		$markupcode = self::parseEmoticons($markupcode);

	    if($textual) {
	        $markupcode = self::parseTextual($markupcode);
		}

    	return $markupcode;
    }

    /**
     * Parses Emoticon-Smileys like ;D
     */
    static function parseEmoticons($markupcode) {
        $smilies = Smileys::getSmileys();
        reset($smilies);
        while (list($code, $url) = each($smilies)) {
            $markupcode = str_replace($code, "<img src=\"".BIGACE_URL_ADDON."/smileys/$url\" border=\"0\" alt=\"" . htmlspecialchars($code) . "\">", $markupcode);
        }
        return $markupcode;
    }

    /**
     * Parses textual Markup-Smileys like :angry:
     */
    static function parseTextual($markupcode) {
        $smilies = Smileys::getTextSmileys();
        reset($smilies);
        while (list($code, $url) = each($smilies)) {
            $markupcode = str_replace($code, "<img src=\"".BIGACE_URL_ADDON."/smileys/$url\" border=\"0\" alt=\"" . htmlspecialchars($code) . "\">", $markupcode);
        }
        return $markupcode;
    }

}