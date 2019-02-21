<?php
namespace App\Tools;

class AngularCli
{
    /**
     * @param string $kernelProjectDir
     * @return array
     */
    public static function getNgBuildFiles(string $kernelProjectDir): array
    {
        // Quick'n a bit dirty solution to load correct ng files (until now, prod build with version string in name cannot be loaded)
        $ngFiles = [
            'js' => [ // take care at the order it's very important !!!
                'runtime' => 'runtime.js',
                'polyfills' => 'polyfills.js',
                'vendor' => 'vendor.js',
                'main' => 'main.js',
            ],
            'css' => [
                'styles' => 'styles.css',
            ]
        ];
        $json = file_get_contents($kernelProjectDir . '/assets/js/form-devxpress-angular/angular.json');
        $jsonDecoded = json_decode($json, true);
        $path = $jsonDecoded['projects']['devxpress']['targets']['build']['options']['outputPath'];
        $pathParts = explode('/', $path);
        $ngBuildDir = [];
        $startBuildBir = false;
        foreach ($pathParts as $oneDir) {
            if ($oneDir === 'public') {
                $startBuildBir = true;
            }

            if (!$startBuildBir) {
                continue;
            }

            $ngBuildDir[] = $oneDir;
        }

        // find build files
        $ngBuildDirFromProject = '/' . implode('/', $ngBuildDir);
        $ngBuildDirFromPublic = '/' . implode('/', array_slice($ngBuildDir, 1));
        $dirIt = new \DirectoryIterator($kernelProjectDir . $ngBuildDirFromProject);
        foreach ($dirIt as $iteration) {
            if ($iteration->isFile()) {
                // improve this using array_* functions
                foreach ($ngFiles['js'] as $jsKey => $jsNgFile) {
                    if (strpos($iteration->getBasename(), $jsKey) !== false) {
                        $ngFiles['js'][$jsKey] = $ngBuildDirFromPublic.$iteration->getBasename();
                        continue;
                    }
                }

                foreach ($ngFiles['css'] as $cssKey => $cssNgFile) {
                    if (strpos($iteration->getBasename(), $cssKey) !== false) {
                        $ngFiles['css'][$cssKey] = $ngBuildDirFromPublic.$iteration->getBasename();
                        continue;
                    }
                }
            }
        }

        return $ngFiles;
    }
}
