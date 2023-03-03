/*
	Check IE
*/
(function()
{
	if(navigator && navigator.appVersion)
	{
		var v = navigator.appVersion,
			pos = v.indexOf('MSIE ');
		if(pos)
		{
			v = v.substr(pos + 5);
			var list = v.split('.', 2);
			if(list.length == 2)
			{
				v = parseInt(list[0]);
				if(v)
				{
					phpBB.ie = v;
				}
			}
		}
	}
})();

$(document).ready(function()
{
	$('.phpbb').addClass('js');

	/*
		Test browser capabilities
	*/
	var rootElement = $('.phpbb');
	if(phpBB.ie) rootElement.addClass('ie' + phpBB.ie);
	rootElement.addClass(phpBB.ie && phpBB.ie < 8 ? 'no-tables' : 'display-table');
	rootElement.addClass(phpBB.ie && phpBB.ie < 9 ? 'no-rgba' : 'has-rgba');

	/*
		Jump box
	*/
	function setupJumpBox()
	{
		var data = $('#jumpbox-data option'),
			list = [],
			levels = {},
			lastLevel = -1;
		if(!data.length || data.length != phpBB.jumpBoxData.length)
		{
			$('#jumpbox-data').remove();
			return false;
		}
		for(var i=0; i<phpBB.jumpBoxData.length; i++)
		if(phpBB.jumpBoxData[i].id >= 0)
		{
			var level = phpBB.jumpBoxData[i].level.length;
			phpBB.jumpBoxData[i].level = level;
			phpBB.jumpBoxData[i].name = $.trim(data.eq(i).text());
			if(!phpBB.jumpBoxData[i].selected) phpBB.jumpBoxData[i].selected = false;
			// find parent item
			levels[level] = list.length;
			lastLevel = level;
			phpBB.jumpBoxData[i].prev = (level > 0) ? levels[level - 1] : -1;
			list.push(phpBB.jumpBoxData[i]);
		}
		phpBB.jumpBoxData = list;
		$('#jumpbox-data').remove();
		return (list.length > 0);
	}
	if(typeof(phpBB.jumpBoxAction) != 'undefined')
	{
		if(setupJumpBox())
		{
			// setup full jumpbox
			var text = phpBB.jumpBoxText(phpBB.jumpBoxData);
			$('.phpbb .nav-jumpbox').each(function()
			{
				$(this).addClass('popup-trigger').append('<div class="popup popup-list">' + text + '</div>');
			});
			$('#jumpbox:has(select):not(#cp-main #jumpbox)').each(function()
			{
				var select = $('select', this).get(0),
					val = (select && select.options.length) ? ((select.selectedIndex > 1) ? select.options[select.selectedIndex].value : select.options[0].value) : '',
					title = (select && select.options.length) ? ((select.selectedIndex > 1) ? select.options[select.selectedIndex].text : select.options[0].text) : '';
				if(val)
				{
					for(var i=0; i<phpBB.jumpBoxData.length; i++)
					{
						if(phpBB.jumpBoxData[i].id > 0 && phpBB.jumpBoxData[i].id == val)
						{
							title = phpBB.jumpBoxData[i].name;
						}
					}
				}
				if(title.length)
				{
					$('input[type="submit"]', this).remove();
					$('select', this).replaceWith('<div class="jumpbox popup-trigger popup-up right"><a class="button" href="javascript:void(0);">' + title + '</a><div class="popup popup-list">' + text + '</div></div>');
					$(this).addClass('jumpbox-js');
				}
			});
			$('.phpbb .nav-forum').each(function()
			{
				function checkLink(link, id)
				{
					// split link
					link += ' ';
					var list = link.split(id),
						total = list.length - 1;
					for(var i=0; i<total; i++)
					if(list[i].length > 0 && list[i + 1].length > 0)
					{
						// check if previous and next characters are numbers
						var char1 = list[i].charCodeAt(list[i].length - 1),
							char2 = list[i + 1].charCodeAt(0);
						if((char1 < 48 || char1 > 57) && (char2 < 48 || char2 > 57)) return true;
					}
					return false;
				}
				
				function findItems(num, showNested)
				{
					var item = phpBB.jumpBoxData[num],
						current = false,
						list = [];
					for(var i=item.prev + 1; i<phpBB.jumpBoxData.length; i++)
					{
						var item2 = phpBB.jumpBoxData[i];
						if(item2.level < item.level)
						{
							// another category
							return list;
						}
						if(item2.level == item.level)
						{
							if(item2.prev != item.prev)
							{
								// belongs to another category
								return list;
							}
							current = (i == num);
							if(current != item2.selected)
							{
								var item2 = $.extend({}, item2, true);
								item2.selected = current;
							}
							list.push(item2);
						}
						else if(showNested && current)
						{
							if(item2.selected)
							{
								var item2 = $.extend({}, item2, true);
								item2.selected = false;
							}
							list.push(item2);
						}
					}
					return list;
				}
				
				var title = $.trim($(this).text()),
					found = [];
				// find all entries with same name
				for(var i=0; i<phpBB.jumpBoxData.length; i++)
				{
					if(phpBB.jumpBoxData[i].name == title)
					{
						found.push(i);
					}
				}
				if(!found.length) return;
				var num = found[0];
				if(found.length > 1)
				{
					var found2 = [],
						link = $('a', this).attr('href');
					// find all entries with same link
					for(var i=0; i<found.length; i++)
					{
						if(checkLink(link, phpBB.jumpBoxData[found[i]].id))
						{
							found2.push(found[i]);
						}
					}
					if(!found2.length) return;
					num = found2[0];
				}
				// found 1 or more items. get items in same category + nested items
				var list = findItems(num, !$(this).hasClass('hide-nested'));
				if(list.length < 2) return;
				// create popup
				var text = phpBB.jumpBoxText(list, phpBB.jumpBoxData[num].level);
				$('a.text', this).addClass('text-popup');
				$(this).addClass('popup-trigger').append('<div class="popup popup-list">' + text + '</div>');
			});
		}
	}
	
	/*
		Headers
	*/
	$('.phpbb .page-content > h2, .phpbb #cp-main > h2').addClass('header');

	/*
		Tables
	*/
	$('.phpbb table.table1').attr('cellspacing', '1');
	
	/*
		Inner blocks
	*/
	$('.post > div, .panel > div').addClass('inner');
	$('.phpbb div.panel div.post, .phpbb div.panel ul.topiclist, .phpbb div.panel table.table1, .phpbb div.panel dl.panel').parents('.phpbb div.panel').addClass('panel-wrapper');
	$('.phpbb #cp-main .panel').each(function()
	{
		var inner = $(this).find('.inner:first');
		if(!inner.length) return;
		if(inner.children().length < 2)
		{
			$(this).hide();
		}
	});
	
	/*
		Popups
	*/
	$('.phpbb .popup input, .phpbb .popup select').focus(function() { $(this).parents('.popup').addClass('active'); }).blur(function() { $(this).parents('.popup').removeClass('active'); });
	
	/*
		Inputs
	*/
	$('.phpbb input[type="text"], .phpbb input[type="password"], .phpbb input[type="email"], .phpbb textarea').change(function() { $(this).toggleClass('not-empty', $(this).val().length > 0); }).each(function()
	{
		$(this).toggleClass('not-empty', $(this).val().length > 0);
	});

	/*
		Forgot password link
	*/
	var item = $('#phpbb-sendpass');
	if(item.length)
	{
		var itemLink = item.find('.data-register').text(),
			itemText = item.find('.data-forgot').text();
		if(itemLink.indexOf('mode=register'))
		{
			item.html('<a class="button2" href="' + itemLink.replace(/mode=register/, 'mode=sendpassword') + '">' + itemText + '</a>').css('display', '');
		}
	}

	/*
		Content size
	*/
	if($('.phpbb .page-content').length)
	{
		phpBB.resizeContent();
		$(window).resize(function() { phpBB.resizeContent(); });
	}
});

/*
	Resize window
*/
phpBB.resizeContent = function()
{
	var content = $('.phpbb .page-content'),
		h = content.height(),
		pageHeight = $('.phpbb').height();
	if(!pageHeight)
	{
		pageHeight = $('.phpbb .content-wrapper').height() + 50;
	}
	var diff = pageHeight - h;
	$('.phpbb .page-content').css('min-height', Math.max(780, Math.floor($(window).height() - diff)) + 'px');
};

/*
	Jump box data
*/
phpBB.jumpBoxText = function(list)
{
	var levelDiff = (arguments.length > 1) ? arguments[1] : 0,
		text = '<ul>',
		count = 0,
		maxLevel = 0,
		lastLevel = -1,
		rows = false,
		noHighlight = false,
		limit = (phpBB.ie && phpBB.ie < 8) ? 0 : (list.length > 30 ? 25 : list.length);
	for(var i=0; i<list.length; i++)
	{
		if(limit > 0 && count >= limit)
		{
			if(!rows)
			{
				text = '<ol><li>' + text;
				rows = true;
			}
			text += '</ul></li><li><ul>';
			count = 0;
		}
		count ++;
		var diff = list[i].level - levelDiff;
		if(diff > 4) diff = 4;
		text += '<li class="nowrap level-' + diff;
		if(diff == 0)
		{
			if(lastLevel != 0) text += ' level-root';
			else noHighlight = true;
		}
		lastLevel = diff;
		if(list[i].selected) text += ' row-new';
		if(list[i].name.length > 40) text += ' long';
		text += '">';
		if(list[i].url)
		{
			text += '<a href="' + list[i].url + '">';
		}
		else
		{
			text += '<a href="javascript:void(0);" onclick="phpBB.jumpBox(' + list[i].id + '); return false;">';
		}
		if(diff > 0)
		{
			maxLevel = Math.max(maxLevel, diff);
			text += '<span class="level">';
			for(var j=0; j<diff; j++) text += '- ';
			text += '</span> ';
		}
		text += list[i].name;
		text += '</a></li>';
	}
	if(rows)
	{
		for(var i=count; i<limit; i++)
		{
			text += '<li class="empty"></li>';
		}
	}
	text += '</ul>' + (rows ? '</li></ol>' : '');
	if(!noHighlight && maxLevel > 0)
	{
		// highlight root categories
		var tag = (list.length > limit) ? 'ol' : 'ul';
		text = text.replace('<' + tag + '>', '<' + tag + ' class="show-levels">');
	}
	return text;
};

phpBB.jumpBox = function(id)
{
	var d = new Date(),
		itemId = 'form-' + d.getTime();
	$('body').after('<div id="' + itemId + '" style="display: none;"><form action="' + phpBB.jumpBoxAction + '" method="post"><input type="hidden" name="f" value="' + id + '" /></form></div>');
	$('#' + itemId + ' form').get(0).submit();
};

