# TODO: Implement Steel Slab Table 3 Configuration

## Plan:
- **Information Gathered**: Extend steel-slab tab with Table 3 below Table 2, same filter logic ($periodYear/$period), STEAM energy_name focus.
- **Plan**:
  1. `app/Livewire/ConfigurationData.php`: Add `$energyRowsTable3` (4 rows):
     | Description | Tooltip | conditions |
     | Export to Coke Plant & Vendor | Sum STEAM from 4 plants | ICK000 Cokes by product plant STEAM, I022B0 Vendor STEAM, I022CA Sales (KPCC, Lime Calcining) STEAM, I022CC Sales (Linde, Oxygen Plant) STEAM |
     | BF | Quantity STEAM IBN000 Blast Furnace Plant STEAM | IBN000 Blast Furnace Plant STEAM |
     | SMP & CCP + Energy | Sum STEAM IEA000 Steel making + ITD120 Utility- By Product Gas distribution | 2 conditions |
     | Total | BF + SMP & CCP + Energy | Computed sum rows 2+3 |
  2. Add `getEnergyTableDataTable3Property()` mirroring Table2, but Total = row1['power'] + row2['power'] (0-index).
  3. `render()`: Pass 'energyTableDataTable3'.
  4. Blade: Below Table 2 div, add Table 3 with identical structure/h6 "Table 3".
- **Dependent Files**: app/Livewire/ConfigurationData.php, resources/views/livewire/configuration-data.blade.php
- **Followup**: Test STEAM data.

Implementation complete.

