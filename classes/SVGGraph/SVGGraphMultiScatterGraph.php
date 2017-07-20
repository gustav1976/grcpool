<?php
/**
 * Copyright (C) 2011-2016 Graham Breach
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * For more information, please contact <graham@goat1000.com>
 */

require_once 'SVGGraphPointGraph.php';
require_once 'SVGGraphMultiGraph.php';

/**
 * MultiScatterGraph - points with axes and grid
 */
class MultiScatterGraph extends PointGraph {

  protected $repeated_keys = 'accept';
  protected $require_integer_keys = false;

  protected function Draw()
  {
    $body = $this->Grid() . $this->UnderShapes();

    // a scatter graph without markers is empty!
    if($this->marker_size == 0)
      $this->marker_size = 1;

    $chunk_count = count($this->multi_graph);
    $this->ColourSetup($this->multi_graph->ItemsCount(-1), $chunk_count);
    $best_fit_above = $best_fit_below = '';
    for($i = 0; $i < $chunk_count; ++$i) {
      $bnum = 0;
      $axis = $this->DatasetYAxis($i);
      foreach($this->multi_graph[$i] as $item) {
        $x = $this->GridPosition($item, $bnum);
        if(!is_null($item->value) && !is_null($x)) {
          $y = $this->GridY($item->value, $axis);
          if(!is_null($y)) {
            $marker_id = $this->MarkerLabel($i, $bnum, $item, $x, $y);
            $extra = empty($marker_id) ? NULL : array('id' => $marker_id);
            $this->AddMarker($x, $y, $item, $extra, $i);
          }
        }
        ++$bnum;
      }
    }

    list($best_fit_above, $best_fit_below) = $this->BestFitLines();
    $body .= $best_fit_below;
    $body .= $this->OverShapes();
    $body .= $this->Axes();
    $body .= $this->CrossHairs();
    $body .= $this->DrawMarkers();
    $body .= $best_fit_above;
    return $body;
  }

  /**
   * Sets up values array
   */
  public function Values($values)
  {
    parent::Values($values);
    if(!$this->values->error)
      $this->multi_graph = new MultiGraph($this->values, $this->force_assoc,
        $this->datetime_keys, $this->require_integer_keys);
  }

  /**
   * Checks that the data produces a 2-D plot
   */
  protected function CheckValues()
  {
    parent::CheckValues();

    // using force_assoc makes things work properly
    if($this->values->AssociativeKeys())
      $this->force_assoc = true;
  }
}

