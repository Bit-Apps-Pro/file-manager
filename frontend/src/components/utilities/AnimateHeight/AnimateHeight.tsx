import { motion } from 'framer-motion'

type AnimateHeightPropsType = {
  children: React.ReactNode
  className?: string
  style?: React.CSSProperties
}

export default function AnimateHeight({ children, className, style }: AnimateHeightPropsType) {
  return (
    <motion.div
      layout
      layoutRoot
      initial={{ height: 0 }}
      animate={{ height: 'auto' }}
      exit={{ height: 0 }}
      transition={{ duration: 0.3 }}
      style={{ overflow: 'hidden', ...style }}
      className={className}
    >
      {children}
    </motion.div>
  )
}
