/**
 * Add a Card created by the CreateCard function to main body;
 * @param {Element} Card 
 */
function AddCard(Card)
{
	document.getElementById('ContentBody').appendChild(Card);
}

/**
 * 
 * @param {String} ID
 * @param {String} Hash
 * @param {String} Color 
 * @param {String} Header
 * @param {String} BodyHTML
 * @param {String} FooterHTML
 * @returns {Element}
 */
function CreateCard(ID, Hash, Header, Color = 'dark', BodyHTML = '', FooterHTML = '')
{
	var Card = document.createElement('div');
	Card.classList += 'card card-' + Color + ' ' + Hash;
	Card.id = ID;
	Card.innerHTML = '<div class="card-header" id="Card-' + ID + 'Header"><h3 class="card-title">' + Header + '</h3></div><div class="card-body" id="' + ID + 'Body">' + BodyHTML + '</div><div class="card-footer" id="' + ID + 'Footer">' + FooterHTML + '</div>';
	return Card;
}

/**
 * Add a Notification created by the CreateNotification function
 * @param {Element} Notification 
 */
function AddNotification(Notification)
{
	if (Notification.IsMinimized && $('#NotificationNumber').length)
	{
		var Not = document.createElement('a');
		Not.href = Notification.Link;
		Not.id = 'Notification-' + Notification.ID + '-Minimized';
		Not.classList = 'dropdown-item';
		Not.innerHTML = '<i class="fas fa-' + Notification.FontAwesomeIcon + ' mr-2"></i>' + Notification.Message;
		
		$('#NotificationNumber').text(parseInt($('#NotificationNumber').text()) + 1);
		$('#NotificationNumberNotifications').text($('#NotificationNumber').text() + ' ' + $('#NotificationNumberNotifications').text().split(' ', 2)[1]);
		$('#NotificationDropDown').append(Not).append($.parseHTML('<div class="dropdown-divider"></div>'));
	}
	if (Notification.IsInstant)
	{
		var Not = $.parseHTML('<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false" id="Notification-' + Notification.id + '-Instant"><div class="toast-header"><i class="fas fa-' + Notification.FontAwesomeIcon + '"></i><strong class="mr-auto">Notification.Type</strong><button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close" onclick="$(\'#Notification-' + Notification.id + '-Instant\').remove();"><span aria-hidden="true">&times;</span></button></div><div class="toast-body">' + Notification.Message + '</div></div>');
		$('#InstantNotification').append(Not);
		$('.toast').toast('show');
	}
}

/**
 * Create a custom notification for the addition to the Notification Bar in the top right corner
 * @param {String} ID 
 * @param {String} Link 
 * @param {String} FontAwesomeIcon 
 * @param {String} Message 
 */
function CreateNotification(ID, Link, FontAwesomeIcon, Message, Type = '', IsInstant = false, IsMinimized = false)
{
	return {
		ID: ID,
		Link: Link,
		FontAwesomeIcon: FontAwesomeIcon,
		Message: Message,
		Type: Type,
		IsInstant: IsInstant,
		IsMinimized: IsMinimized
	};
}