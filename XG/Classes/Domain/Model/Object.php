<?php
//
// Copyright (C) 2012 Lars Formella <ich@larsformella.de>
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//

class XG_Domain_Model_Object
{
	/** @var string */
	public $Guid;
	/** @var string */
	public $ParentGuid;
	/** @var int */
	public $Enabled;
	/** @var int */
	public $Connected;
	/** @var string */
	public $Name;
	/** @var int */
	public $LastModified;
	/** @var string */
	public $IrcLink;
}
