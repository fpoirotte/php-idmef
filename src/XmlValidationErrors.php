<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF;

class XmlValidationErrors extends \Exception
{
    protected $validationErrors;

    public function __construct(array $errors)
    {
        $lvl = \LIBXML_ERR_WARNING;
        foreach ($errors as $error) {
            if (!is_object($error) || !($error instanceof \libXMLError)) {
                throw new \InvalidArgumentException('Not validation errors');
            }
            $lvl = max($lvl, $error->level);
        }

        $this->validationErrors = $errors;
        parent::__construct('Validation errors', $lvl);
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function __toString(): string
    {
        $msg = "Validation errors:\n\n";
        $levels = array(
            \LIBXML_ERR_WARNING => "WARNING",
            \LIBXML_ERR_ERROR   => "ERROR",
            \LIBXML_ERR_FATAL   => "FATAL",
        );
        foreach ($this->validationErrors as $error) {
            $level = $levels[$error->level];
            $message = rtrim($error->message);
            $msg .= "[$level:{$error->code}] {$error->file}:{$error->line}:{$error->column}: {$message}\n";
        }
        return $msg;
    }

    public static function raiseOnValidationErrors(array $errors, int $level = \LIBXML_ERR_WARNING): void
    {
        $lvl = \LIBXML_ERR_WARNING;
        foreach ($errors as $error) {
            if (!is_object($error) || !($error instanceof \libXMLError)) {
                throw new \InvalidArgumentException('Not validation errors');
            }
            $lvl = max($lvl, $error->level);
        }

        if ($lvl >= $level) {
            throw new static($errors);
        }
    }
}
