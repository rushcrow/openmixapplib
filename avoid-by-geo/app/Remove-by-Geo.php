<?php

/**
 * Demonstrates incorporating client source country into provider choice
 * Use Performance data for most decsisions, but override for certain countries.
 */

class OpenmixApplication implements Lifecycle
{
    /**
     * @var The list of available CNAMEs, keyed by alias.
     */
    public $servers = array(
        'cdn1' => 'orig.example.cdn1.net',
        'cdn2' => 'www.example.com.cdn2.net');
    
    private $reasons = array(
        'Best performing server selected' => 'A',
        'Data problem' => 'B',
        'All servers eliminated' => 'C');
    
    private $ttl = 30;
    
    private $availabilityThreshold = 90;
    
    /**
     * @param Configuration $config
     */
    public function init($config)
    {
        $config->declareInput(
            RadarProbeTypes::HTTP_RTT,
            implode(',', array_keys($this->servers)));
        
        $config->declareInput(
            RadarProbeTypes::AVAILABILITY,
            implode(',', array_keys($this->servers)));

    $config->declareInput(GeoProperties::COUNTRY);
        
        foreach ($this->servers as $alias => $cname)
        {
            $config->declareResponseOption($alias, $cname, $this->ttl);
        }
        
        foreach ($this->reasons as $code)
        {
            $config->declareReasonCode($code);
        }
    }
    
    /**
     * @param Request $request
     * @param Response $response
     * @param Utilities $utilities
     */
    public function service($request, $response, $utilities)
    {
        $rtt = $request->radar(RadarProbeTypes::HTTP_RTT);
        $country = $request->geo(GeoProperties::COUNTRY);

        if ( $country != 'CN' )
        {
            unset ($rtt["cdn2"]);
        }

        if (is_array($rtt)) {
            $candidates = array_intersect_key($rtt, $this->servers);
            if (0 < count($candidates))
            {
                // Select the best performing server that meets its minimum
                // availability score, if given
                asort($candidates);
                $avail = $request->radar(RadarProbeTypes::AVAILABILITY);
                foreach (array_keys($candidates) as $alias)
                {
                    if ($avail[$alias] >= $this->availabilityThreshold)
                    {
                        $response->selectProvider($alias);
                        $response->setReasonCode($this->reasons['Best performing server selected']);
                        return;
                    }
                }
                $response->setReasonCode($this->reasons['All servers eliminated']);
            }
            else
            {
                $response->setReasonCode($this->reasons['Data problem']);
            }
        }
        else
        {
            $response->setReasonCode($this->reasons['Data problem']);
        }
        $utilities->selectRandom();
    }
}
?>
