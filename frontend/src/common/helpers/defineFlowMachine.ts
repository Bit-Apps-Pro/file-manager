import ComponentName from '@common/globalStates/flows/ComponentNameType'
import {
  type FlowMachineRootType,
  type FlowMachineType
} from '@common/globalStates/flows/FlowMachineType'

import {
  isConnectionComponent,
  isInputComponent,
  isMixInputComponent,
  isRepeaterFieldComponent,
  isSelectComponent,
  isWebhookComponent
} from './flowMachineUtils'

const helpers = {
  componentName: ComponentName,
  isInputComponent,
  isMixInputComponent,
  isSelectComponent,
  isConnectionComponent,
  isWebhookComponent,
  isRepeaterFieldComponent
} as const

export type HelpersType = typeof helpers

interface DefineMachineParamsType {
  helpers: HelpersType
}

export function defineFlowMachine(
  machineConfigCallback: ({ helpers }: DefineMachineParamsType) => FlowMachineType
): FlowMachineType {
  return machineConfigCallback({ helpers })
}

export function defineFlowMachineRoot(machineConfig: FlowMachineRootType): FlowMachineRootType {
  return machineConfig
}
