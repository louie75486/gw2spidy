<?php

namespace Igorw\Silex;

use Symfony\Component\Yaml\Yaml;

class Config {
    private $env;
    private $cnfdir;
    private $config       = null;
    private $replacements = array();

    public function __construct(Env $env, $cnfdir, array $replacements = array()) {
        $this->env    = $env;
        $this->cnfdir = $cnfdir;
    }

    public function getConfig() {
        if (is_null($this->config)) {
            $this->buildConfig();
        }

        return $this->config;
    }

    private function buildConfig() {
        $config = array();

        foreach ($this->env->getEnvs() as $env) {
            $config += $this->readConfig("{$this->cnfdir}/{$env}.cnf.json");
        }

        if ($replacements) {
            foreach ($replacements as $key => $value) {
                $this->replacements['%'.$key.'%'] = $value;
            }
        }

        foreach ($config as $k => $v) {
            $config[$k] = $this->doReplacements($v);
        }

        $this->config = $config;
    }

    private function doReplacements($value) {
        if (!$this->replacements) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->doReplacements($v);
            }

            return $value;
        }

        if (is_string($value)) {
            return strtr($value, $this->replacements);
        }

        return $value;
    }

    private function readConfig($filename) {
        $format = $this->getFileFormat($filename);

        if (!$filename || !$format) {
            throw new \RuntimeException('A valid configuration file must be passed before reading the config.');
        }

        if (!file_exists($filename)) {
            throw new \InvalidArgumentException(
                sprintf("The config file '%s' does not exist.", $filename));
        }

        if ('yaml' === $format) {
            if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
                throw new \RuntimeException('Unable to read yaml as the Symfony Yaml Component is not installed.');
            }
            return Yaml::parse($filename);
        }

        if ('json' === $format) {
            return json_decode(file_get_contents($filename), true);
        }

        throw new \InvalidArgumentException(
                sprintf("The config file '%s' appears has invalid format '%s'.", $filename, $format));
    }

    public function getFileFormat($filename) {
        if (preg_match('#.ya?ml(.dist)?$#i', $filename)) {
            return 'yaml';
        }

        if (preg_match('#.json(.dist)?$#i', $filename)) {
            return 'json';
        }

        return pathinfo($filename, PATHINFO_EXTENSION);
    }
}
