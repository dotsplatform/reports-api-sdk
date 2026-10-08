<?php

declare(strict_types=1);

/**
 * Description of ReportsNotFoundException.php
 *
 * 404: the account, report definition, run, snapshot or api client does not exist, or belongs to another account
 * (the reports service never confirms ids of other accounts).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Exceptions;

class ReportsNotFoundException extends ReportsApiException
{
}
