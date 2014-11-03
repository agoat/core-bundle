 * A class to access the file system
 * The class handles file operations via the PHP functions.
class Files
			self::$objInstance = new static();
	public function mkdir($strDirectory)
	{
		$this->validate($strDirectory);
		return @mkdir(TL_ROOT . '/' . $strDirectory);
	}
	public function rmdir($strDirectory)
	{
		$this->validate($strDirectory);
		return @rmdir(TL_ROOT. '/' . $strDirectory);
	}
			if (is_link(TL_ROOT . '/' . $strFolder . '/' . $strFile))
			{
				$this->delete($strFolder . '/' . $strFile);
			}
			elseif (is_dir(TL_ROOT . '/' . $strFolder . '/' . $strFile))
	public function fopen($strFile, $strMode)
	{
		$this->validate($strFile);
		return @fopen(TL_ROOT . '/' . $strFile, $strMode);
	}
	public function fputs($resFile, $strContent)
	{
		@fputs($resFile, $strContent);
	}
	public function fclose($resFile)
	{
		return @fclose($resFile);
	}
	public function rename($strOldName, $strNewName)
	{
		// Source file == target file
		if ($strOldName == $strNewName)
		{
			return true;
		}

		$this->validate($strOldName, $strNewName);

		// Windows fix: delete the target file
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && file_exists(TL_ROOT . '/' . $strNewName))
		{
			$this->delete($strNewName);
		}

		// Unix fix: rename case sensitively
		if (strcasecmp($strOldName, $strNewName) === 0 && strcmp($strOldName, $strNewName) !== 0)
		{
			@rename(TL_ROOT . '/' . $strOldName, TL_ROOT . '/' . $strOldName . '__');
			$strOldName .= '__';
		}

		return @rename(TL_ROOT . '/' . $strOldName, TL_ROOT . '/' . $strNewName);
	}
	public function copy($strSource, $strDestination)
	{
		$this->validate($strSource, $strDestination);
		return @copy(TL_ROOT . '/' . $strSource, TL_ROOT . '/' . $strDestination);
	}
	/**
	 * Generate a symlink
	 *
	 * @param string $strSource The symlink name
	 * @param string $strTarget The symlink target
	 *
	 * @throws \Exception If the symlink cannot be created
	 */
	public function symlink($strSource, $strTarget)
	{
		// Check the source
		if ($strSource == '')
		{
			throw new \Exception('No symlink name provided');
		}

		// Check the target
		if ($strTarget == '')
		{
			throw new \Exception('No symlink target provided');
		}
		elseif (strpos($strTarget, '../') !== false)
		{
			throw new \Exception('The symlink target must not be relative');
		}

		// Remove an existing symlink
		if (file_exists(TL_ROOT . '/' . $strTarget))
		{
			if (!is_link(TL_ROOT . '/' . $strTarget))
			{
				throw new \Exception("The target $strTarget exists and is not a symlink");
			}

			unlink(TL_ROOT . '/' . $strTarget);
		}

		$strParent = dirname($strTarget);

		// Create the parent folder
		if (!is_dir(TL_ROOT . '/' . $strParent))
		{
			mkdir(TL_ROOT . '/' . $strParent, 0777, true);
		}

		// Create the symlink
		symlink($strSource, TL_ROOT . '/' . $strTarget);

		// Get the symlink stats
		$stat = lstat(TL_ROOT . '/' . $strTarget);

		// Try to fix the UID
		if ($stat['uid'] != getmyuid())
		{
			if (function_exists('lchown'))
			{
				lchown(TL_ROOT . '/' . $strTarget, getmyuid());
			}
		}

		// Try to fix the GID
		if ($stat['gid'] != getmygid())
		{
			if (function_exists('lchgrp'))
			{
				lchgrp(TL_ROOT . '/' . $strTarget, getmygid());
			}
		}
	}


	public function delete($strFile)
	{
		$this->validate($strFile);
		return @unlink(TL_ROOT . '/' . $strFile);
	}
	public function chmod($strFile, $varMode)
	{
		$this->validate($strFile);
		return @chmod(TL_ROOT . '/' . $strFile, $varMode);
	}
	public function is_writeable($strFile)
	{
		$this->validate($strFile);
		return @is_writeable(TL_ROOT . '/' . $strFile);
	}
	public function move_uploaded_file($strSource, $strDestination)
	{
		$this->validate($strSource, $strDestination);
		return @move_uploaded_file($strSource, TL_ROOT . '/' . $strDestination);
	}