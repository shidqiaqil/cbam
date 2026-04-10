# Task: Add Export Electricity Table below Import

## Steps:
- [x] Step 1: Add $energyRowsExportElectricity array and getEnergyTableDataExportElectricityProperty() in ConfigurationData.php
- [x] Step 2: Add blade render after Import table
- [x] Step 3: Update render()
- [x] Step 4: Clear caches
- [x] Step 5: Test ✓

## Table Rows:
1. **Reverse Power**: plant_code=I02300, plant_name='Reverse Power', criteria=CONSUMPTION, energy_name=POWER (unit kWh)
2. **Export to Coke Plant**: ICA000+Cokes plant + ICK000+Cokes by product plant (POWER, CONSUMPTION) kWh
3. **Total Power Sales to tenant**: I022B0+Vendor + I022CA+Sales (KPCC, Lime Calcining) + I022CC+Sales (Linde, Oxygen Plant) + I022CD+Sales (KDL for Excess Power) + I022D0+Other (free incharge) (POWER, CONSUMPTION) kWh
4. **Reverse Power/1000**: Reverse Power value / 1000 (unit MWh)

**Status**: Ready to implement.
